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

    public function __construct(CacheManager $cacheManager, LoggerInterface $logger)
    {
        $this->cacheManager = $cacheManager;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            HttpCacheEvent::EVENT_NAME => [
                ['clearCache', 0],
            ],
        ];
    }

    public function clearCache(HttpCacheEvent $event)
    {
        switch (true) {
            case $event->getSubject() instanceof ContainerInterface:
                $this->cacheManager->invalidateRoute('swp_api_templates_list_containers');
                $this->cacheManager->invalidateRoute('swp_api_templates_get_container', [
                    'uuid' => $event->getSubject()->getUuid(),
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
