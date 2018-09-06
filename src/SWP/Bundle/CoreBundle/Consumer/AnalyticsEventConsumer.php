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

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use SWP\Bundle\AnalyticsBundle\Model\ArticleEventInterface;
use SWP\Bundle\AnalyticsBundle\Services\ArticleStatisticsServiceInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Resolver\TenantResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * Class AnalyticsEventConsumer.
 */
class AnalyticsEventConsumer implements ConsumerInterface
{
    /**
     * @var ArticleStatisticsServiceInterface
     */
    protected $articleStatisticsService;

    /**
     * @var TenantResolver
     */
    protected $tenantResolver;

    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * @var UrlMatcherInterface
     */
    protected $matcher;

    /**
     * AnalyticsEventConsumer constructor.
     *
     * @param ArticleStatisticsServiceInterface $articleStatisticsService
     * @param TenantResolver                    $tenantResolver
     * @param TenantContextInterface            $tenantContext
     */
    public function __construct(
        ArticleStatisticsServiceInterface $articleStatisticsService,
        TenantResolver $tenantResolver,
        TenantContextInterface $tenantContext,
        UrlMatcherInterface $matcher
    ) {
        $this->articleStatisticsService = $articleStatisticsService;
        $this->tenantResolver = $tenantResolver;
        $this->tenantContext = $tenantContext;
        $this->matcher = $matcher;
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

        $this->setTenant($request);

        if ($request->query->has('articleId')) {
            $this->handleArticlePageviews($request);
        }

        if ($request->attributes->has('data') && ArticleEventInterface::ACTION_IMPRESSION === $request->query->get('type')) {
            $this->handleArticleImpressions($request);
        }

        return ConsumerInterface::MSG_ACK;
    }

    private function handleArticleImpressions(Request $request): void
    {
        $articles = [];
        $extraData = [];
        foreach ($request->attributes->get('data') as $url) {
            try {
                $route = $this->matcher->match($this->getPathFromUrl($url));
                if (isset($route['_article_meta']) && $route['_article_meta']->getValues() instanceof ArticleInterface) {
                    $articleId = $route['_article_meta']->getValues()->getId();
                    if (!\array_key_exists($articleId, $articles)) {
                        $articles[$articleId] = $route['_article_meta']->getValues();
                    }
                }
            } catch (ResourceNotFoundException $e) {
                //ignore
            }
        }

        foreach ($articles as $article) {
            $this->articleStatisticsService->addArticleEvent(
                (int) $article->getId(),
                ArticleEventInterface::ACTION_IMPRESSION,
                $this->getImpressionSource($request)
            );
        }
    }

    private function getImpressionSource(Request $request): array
    {
        $source = [];
        $referrer = $request->server->get('HTTP_REFERER');
        if (null === $referrer) {
            return $source;
        }

        $route = $this->matcher->match($this->getPathFromUrl($referrer));
        if (isset($route['_article_meta']) && $route['_article_meta']->getValues() instanceof ArticleInterface) {
            $source['type'] = 'article';
            $source['sourceArticle'] = $route['_article_meta']->getValues();
        } elseif (isset($route['_route_meta']) && $route['_route_meta']->getValues() instanceof RouteInterface) {
            $source['type'] = 'route';
            $source['sourceRoute'] = $route['_route_meta']->getValues();
        } elseif (isset($route['_route']) && 'homepage' === $route['_route']) {
            $source['type'] = 'homepage';
        }

        return $source;
    }

    private function getPathFromUrl(string $url): string
    {
        $fragments = \parse_url($url);

        return str_replace('/app_dev.php', '', $fragments['path']);
    }

    private function handleArticlePageviews(Request $request): void
    {
        $articleId = $request->query->get('articleId', null);
        if (null !== $articleId) {
            $this->articleStatisticsService->addArticleEvent((int) $articleId, ArticleEventInterface::ACTION_PAGEVIEW, []);
        }
    }

    /**
     * @param Request $request
     */
    private function setTenant(Request $request): void
    {
        $this->tenantContext->setTenant($this->tenantResolver->resolve($request->getHost()));
    }
}
