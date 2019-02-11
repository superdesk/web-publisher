<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use FOS\HttpCache\Exception\ExceptionCollection;
use FOS\HttpCacheBundle\CacheManager;
use Psr\Log\LoggerInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Component\Common\Event\HttpCacheEvent;
use SWP\Bundle\CoreBundle\Model\ContainerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HttpCacheSubscriber implements EventSubscriberInterface
{
    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * HttpCacheSubscriber constructor.
     *
     * @param CacheManager    $cacheManager
     * @param LoggerInterface $logger
     */
    public function __construct(CacheManager $cacheManager, LoggerInterface $logger, TenantContextInterface $tenantContext)
    {
        $this->cacheManager = $cacheManager;
        $this->logger = $logger;
        $this->tenantContext = $tenantContext;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            HttpCacheEvent::EVENT_NAME => [
                ['clearCache', 0],
            ],
        ];
    }

    public function clearCache(HttpCacheEvent $event): void
    {
        $headers = ['host' => $this->getHostName($this->tenantContext->getTenant())];
        switch (true) {
            case $event->getSubject() instanceof ContainerInterface:
                $this->cacheManager->invalidateRoute('swp_api_templates_list_containers', [], $headers);
                $this->cacheManager->invalidateRoute('swp_api_templates_get_container', [
                    'uuid' => $event->getSubject()->getUuid(),
                ], $headers);

                break;

            case $event->getSubject() instanceof ArticleInterface:
                /** @var ArticleInterface $article */
                $article = $event->getSubject();
                if (ArticleInterface::STATUS_PUBLISHED === $article->getStatus() &&
                    $article->getPublishedAt() >= (new \DateTime('now'))->modify('-1 hour')
                ) {
                    if (null !== $article->getRoute()) {
                        $this->cacheManager->invalidateRoute($article, [], $headers);
                        $this->cacheManager->invalidateRoute($article->getRoute(), [], $headers);
                    }
                    $this->cacheManager->invalidateRoute('swp_api_content_list_articles', [], $headers);
                    $this->cacheManager->invalidateRoute('swp_api_content_show_articles', [
                        'id' => $article->getId(),
                    ], $headers);

                    $this->cacheManager->invalidateRoute('homepage', [], $headers);
                }

                break;
        }

        try {
            $this->cacheManager->flush();
        } catch (ExceptionCollection $e) {
            $this->logger->error($e->getMessage());
        }
    }

    private function getHostName(TenantInterface $tenant): string
    {
        $hostName = $tenant->getDomainName();

        if (null !== $tenant->getSubdomain()) {
            $hostName = $tenant->getSubdomain().'.'.$hostName;
        }

        return $hostName;
    }
}
