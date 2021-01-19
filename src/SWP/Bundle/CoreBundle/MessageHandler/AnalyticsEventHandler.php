<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\MessageHandler;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\ElasticaBundle\Persister\ObjectPersisterInterface;
use SWP\Bundle\AnalyticsBundle\Messenger\AnalyticsEvent;
use SWP\Bundle\AnalyticsBundle\Model\ArticleEventInterface;
use SWP\Bundle\AnalyticsBundle\Services\ArticleStatisticsServiceInterface;
use SWP\Bundle\CoreBundle\Model\ArticleStatistics;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Resolver\TenantResolver;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AnalyticsEventHandler implements MessageHandlerInterface
{
    /** @var ArticleStatisticsServiceInterface */
    private $articleStatisticsService;

    /** @var TenantResolver */
    private $tenantResolver;

    /** @var TenantContextInterface */
    private $tenantContext;

    /** @var ObjectManager */
    private $articleStatisticsObjectManager;

    /**
     * @var ObjectPersisterInterface
     */
    private $elasticaObjectPersister;

    public function __construct(
        ArticleStatisticsServiceInterface $articleStatisticsService,
        TenantResolver $tenantResolver,
        TenantContextInterface $tenantContext,
        ObjectManager $articleStatisticsObjectManager,
        ObjectPersisterInterface $elasticaObjectPersister
    ) {
        $this->articleStatisticsService = $articleStatisticsService;
        $this->tenantResolver = $tenantResolver;
        $this->tenantContext = $tenantContext;
        $this->articleStatisticsObjectManager = $articleStatisticsObjectManager;
        $this->elasticaObjectPersister = $elasticaObjectPersister;
    }

    public function __invoke(AnalyticsEvent $analyticsEvent)
    {
        $this->setTenant($analyticsEvent->getHttpReferrer());

        $articleId = $analyticsEvent->getArticleId();
        $this->handleArticlePageViews($articleId, $analyticsEvent->getPageViewReferrer());
    }

    private function handleArticlePageViews(int $articleId, ?string $pageViewReferrer): void
    {
        if (0 !== $articleId) {
            $articleStatistics = $this->articleStatisticsService->addArticleEvent($articleId, ArticleEventInterface::ACTION_PAGEVIEW, [
                'pageViewSource' => $this->getPageViewSource($pageViewReferrer),
            ]);
            $query = $this->articleStatisticsObjectManager->createQuery('UPDATE '.ArticleStatistics::class.' s SET s.pageViewsNumber = s.pageViewsNumber + 1 WHERE s.id = :id');
            $query->setParameter('id', $articleStatistics->getId());
            $query->execute();

            $this->articleStatisticsObjectManager->clear();

            $this->elasticaObjectPersister->replaceOne($articleStatistics->getArticle());
        }
    }

    private function getPageViewSource(?string $pageViewReferer): string
    {
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

    private function setTenant(string $httpReferrer): void
    {
        $tenant = $this->tenantResolver->resolve($httpReferrer);
        $this->tenantContext->setTenant($tenant);
    }
}
