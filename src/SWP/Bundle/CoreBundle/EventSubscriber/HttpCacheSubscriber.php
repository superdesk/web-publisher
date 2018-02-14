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
     * HttpCacheSubscriber constructor.
     *
     * @param CacheManager    $cacheManager
     * @param LoggerInterface $logger
     */
    public function __construct(CacheManager $cacheManager, LoggerInterface $logger)
    {
        $this->cacheManager = $cacheManager;
        $this->logger = $logger;
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

    /**
     * @param HttpCacheEvent $event
     */
    public function clearCache(HttpCacheEvent $event): void
    {
        switch (true) {
            case $event->getSubject() instanceof ContainerInterface:
                $this->cacheManager->invalidateRoute('swp_api_templates_list_containers');
                $this->cacheManager->invalidateRoute('swp_api_templates_get_container', [
                    'uuid' => $event->getSubject()->getUuid(),
                ]);

                break;

            case $event->getSubject() instanceof ArticleInterface:
                if (null !== $event->getSubject()->getRoute()) {
                    $this->cacheManager->invalidateRoute($event->getSubject());
                    $this->cacheManager->invalidateRoute($event->getSubject()->getRoute());
                }
                $this->cacheManager->invalidateRoute('homepage');
                $this->cacheManager->invalidateRoute('swp_api_content_list_articles');
                $this->cacheManager->invalidateRoute('swp_api_content_show_articles', [
                    'id' => $event->getSubject()->getId(),
                ]);

                break;
        }

        try {
            $this->cacheManager->flush();
        } catch (ExceptionCollection $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
