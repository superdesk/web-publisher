<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Consumer;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Persistence\ObjectManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use SWP\Bundle\AnalyticsBundle\Model\ArticleEventInterface;
use SWP\Bundle\AnalyticsBundle\Services\ArticleStatisticsServiceInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\ArticleStatistics;
use SWP\Bundle\CoreBundle\Resolver\ArticleResolverInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Resolver\TenantResolver;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * Class AnalyticsEventConsumer.
 */
final class AnalyticsEventConsumer implements ConsumerInterface
{
    /**
     * @var ArticleStatisticsServiceInterface
     */
    private $articleStatisticsService;

    /**
     * @var TenantResolver
     */
    private $tenantResolver;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var UrlMatcherInterface
     */
    private $matcher;

    /**
     * @var ArticleResolverInterface
     */
    private $articleResolver;

    private $articleStatisticsObjectManager;

    private $cacheProvider;

    public function __construct(
        ArticleStatisticsServiceInterface $articleStatisticsService,
        TenantResolver $tenantResolver,
        TenantContextInterface $tenantContext,
        UrlMatcherInterface $matcher,
        ArticleResolverInterface $articleResolver,
        ObjectManager $articleStatisticsObjectManager,
        CacheProvider $cacheProvider
    ) {
        $this->articleStatisticsService = $articleStatisticsService;
        $this->tenantResolver = $tenantResolver;
        $this->tenantContext = $tenantContext;
        $this->matcher = $matcher;
        $this->articleResolver = $articleResolver;
        $this->articleStatisticsObjectManager = $articleStatisticsObjectManager;
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @param AMQPMessage $message
     *
     * @return bool|mixed
     */
    public function execute(AMQPMessage $message)
    {
        /** @var Request $request */
        $request = unserialize($message->getBody());
        if (!$request instanceof Request) {
            return ConsumerInterface::MSG_REJECT;
        }

        try {
            $this->setTenant($request);
        } catch (TenantNotFoundException $e) {
            echo $e->getMessage()."\n";

            return ConsumerInterface::MSG_REJECT;
        }
        echo 'Set tenant: '.$this->tenantContext->getTenant()->getCode()."\n";

        if ($request->query->has('articleId')) {
            $this->handleArticlePageViews($request);

            echo 'Pageview for article '.$request->query->get('articleId')." was processed \n";
        }

        if ($request->attributes->has('data') && ArticleEventInterface::ACTION_IMPRESSION === $request->query->get('type')) {
            $this->handleArticleImpressions($request);

            echo "Article impressions were processed \n";
        }

        return ConsumerInterface::MSG_ACK;
    }

    private function handleArticleImpressions(Request $request): void
    {
        $articles = [];
        if (!\is_array($request->attributes->get('data'))) {
            return;
        }

        foreach ($request->attributes->get('data') as $articleId) {
            $cacheKey = md5('article_impressions_'.$articleId);

            if ($this->cacheProvider->contains($cacheKey)) {
                $articleId = $this->cacheProvider->fetch($cacheKey);
            } elseif (filter_var($articleId, FILTER_VALIDATE_URL)) {
                try {
                    $article = $this->articleResolver->resolve($articleId);
                    if (null === $article) {
                        continue;
                    }
                } catch (\Exception $e) {
                    continue;
                }

                $articleId = $article->getId();
                $this->cacheProvider->save($cacheKey, $articleId);
            }

            if (!\array_key_exists($articleId, $articles)) {
                $articles[] = $articleId;
            }
        }

        $impressionSource = $this->getImpressionSource($request);

        $this->articleStatisticsObjectManager->getConnection()->beginTransaction();

        try {
            $ids = [];
            foreach ($articles as $articleId) {
                $articleStatistics = $this->articleStatisticsService->addArticleEvent(
                    (int) $articleId,
                    ArticleEventInterface::ACTION_IMPRESSION,
                    $impressionSource
                );

                $ids[] = $articleStatistics->getId();
                echo 'Article '.$articleId." impression was added \n";
            }

            try {
                $stmt = $this->articleStatisticsObjectManager->getConnection()->prepare('LOCK TABLE swp_article_statistics IN EXCLUSIVE MODE;');
                $stmt->execute();
            } catch (\Exception $e) {
                // ignore when lock not supported
            }

            $query = $this->articleStatisticsObjectManager->createQuery('UPDATE '.ArticleStatistics::class.' s SET s.impressionsNumber = s.impressionsNumber + 1 WHERE s.id IN (:ids)');
            $query->setParameter('ids', $ids);
            $query->execute();

            $this->articleStatisticsObjectManager->flush();
            $this->articleStatisticsObjectManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->articleStatisticsObjectManager->getConnection()->rollBack();

            throw $e;
        }
    }

    private function handleArticlePageViews(Request $request): void
    {
        $articleId = $request->query->get('articleId', null);
        if (null !== $articleId && 0 !== (int) $articleId) {
            $articleStatistics = $this->articleStatisticsService->addArticleEvent((int) $articleId, ArticleEventInterface::ACTION_PAGEVIEW, [
                'pageViewSource' => $this->getPageViewSource($request),
            ]);

            $query = $this->articleStatisticsObjectManager->createQuery('UPDATE '.ArticleStatistics::class.' s SET s.pageViewsNumber = s.pageViewsNumber + 1 WHERE s.id = :id');
            $query->setParameter('id', $articleStatistics->getId());
            $query->execute();
        }
    }

    private function getImpressionSource(Request $request): array
    {
        $source = [];
        $referrer = $request->server->get('HTTP_REFERER');

        if (null === $referrer) {
            return $source;
        }

        try {
            $route = $this->matcher->match($this->getFragmentFromUrl($referrer, 'path'));
        } catch (ResourceNotFoundException $e) {
            return $source;
        }

        if (isset($route['_article_meta']) && $route['_article_meta'] instanceof Meta && $route['_article_meta']->getValues() instanceof ArticleInterface) {
            $source[ArticleStatisticsServiceInterface::KEY_IMPRESSION_TYPE] = 'article';
            $source[ArticleStatisticsServiceInterface::KEY_IMPRESSION_SOURCE_ARTICLE] = $route['_article_meta']->getValues();
        } elseif (isset($route['_route_meta']) && $route['_route_meta'] instanceof Meta && $route['_route_meta']->getValues() instanceof RouteInterface) {
            $source[ArticleStatisticsServiceInterface::KEY_IMPRESSION_TYPE] = 'route';
            $source[ArticleStatisticsServiceInterface::KEY_IMPRESSION_SOURCE_ROUTE] = $route['_route_meta']->getValues();
        } elseif (isset($route['_route']) && 'homepage' === $route['_route']) {
            $source[ArticleStatisticsServiceInterface::KEY_IMPRESSION_TYPE] = 'homepage';
        }

        return $source;
    }

    private function getPageViewSource(Request $request): string
    {
        $pageViewReferer = $request->query->get('ref', null);
        if (null !== $pageViewReferer) {
            $refererHost = $this->getFragmentFromUrl($pageViewReferer, 'host');
            if ($refererHost && $this->isHostMatchingTenant($refererHost)) {
                return ArticleEventInterface::PAGEVIEW_SOURCE_INTERNAL;
            }
        }

        return ArticleEventInterface::PAGEVIEW_SOURCE_EXTERNAL;
    }

    private function getFragmentFromUrl(string $url, string $fragment): ?string
    {
        $fragments = \parse_url($url);
        if (!\array_key_exists($fragment, $fragments)) {
            return null;
        }

        return str_replace('/app_dev.php', '', $fragments[$fragment]);
    }

    private function isHostMatchingTenant(string $host): bool
    {
        $tenant = $this->tenantContext->getTenant();
        $tenantHost = $tenant->getDomainName();
        if (null !== ($subdomain = $tenant->getSubdomain())) {
            $tenantHost = $subdomain.'.'.$tenantHost;
        }

        return $host === $tenantHost;
    }

    /**
     * @param Request $request
     */
    private function setTenant(Request $request): void
    {
        $this->tenantContext->setTenant(
            $this->tenantResolver->resolve(
                $request->server->get('HTTP_REFERER',
                    $request->query->get('host',
                        $request->getHost()
                    )
                )
            )
        );
    }
}
