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

use Doctrine\Common\Persistence\ObjectManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use SWP\Bundle\AnalyticsBundle\Model\ArticleEventInterface;
use SWP\Bundle\AnalyticsBundle\Services\ArticleStatisticsServiceInterface;
use SWP\Bundle\CoreBundle\Model\ArticleStatistics;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Resolver\TenantResolver;
use Symfony\Component\HttpFoundation\Request;

final class AnalyticsEventConsumer implements ConsumerInterface
{
    private $articleStatisticsService;

    private $tenantResolver;

    private $tenantContext;

    private $articleStatisticsObjectManager;

    public function __construct(
        ArticleStatisticsServiceInterface $articleStatisticsService,
        TenantResolver $tenantResolver,
        TenantContextInterface $tenantContext,
        ObjectManager $articleStatisticsObjectManager
    ) {
        $this->articleStatisticsService = $articleStatisticsService;
        $this->tenantResolver = $tenantResolver;
        $this->tenantContext = $tenantContext;
        $this->articleStatisticsObjectManager = $articleStatisticsObjectManager;
    }

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

        return ConsumerInterface::MSG_ACK;
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

        return $fragments[$fragment];
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
