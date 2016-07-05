<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\EventSubscriber;

use FOS\HttpCache\Exception\ExceptionCollection;
use SWP\Bundle\ContentBundle\Document\Route;
use SWP\Bundle\TemplateEngineBundle\Model\Container;
use SWP\Component\Common\Event\HttpCacheEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class HttpCacheSubscriber implements EventSubscriberInterface
{
    protected $cacheManager;

    protected $logger;

    public function __construct($cacheManager, $logger)
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
        if ($event->getSubject() instanceof Container) {
            $this->cacheManager->invalidateRoute('swp_api_templates_list_containers');
            $this->cacheManager->invalidateRoute('swp_api_templates_get_container', [
                'id' => $event->getSubject()->getId(),
            ]);
        }

        if ($event->getSubject() instanceof Route) {
            $this->cacheManager->invalidateRoute('swp_api_content_list_routes');
            $this->cacheManager->invalidateRoute('swp_api_content_show_routes', [
                'id' => $event->getSubject()->getPath(),
            ]);
        }

        try {
            $this->cacheManager->flush();
        } catch (ExceptionCollection $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
