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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use SWP\Component\Common\Event\HttpCacheEvent;
use SWP\Bundle\TemplateEngineBundle\Model\Container;
use FOS\HttpCache\Exception\ExceptionCollection;

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
               ['clearContainers', 0]
           ]
       ];
    }

    public function clearContainers(HttpCacheEvent $event)
    {
        $this->cacheManager->invalidateRoute('swp_api_templates_list_containers');
        if ($event->getSubject() instanceof Container) {
            $this->cacheManager->invalidateRoute('swp_api_templates_get_container', [
                'id' => $event->getSubject()->getId()
            ]);
        }

        try {
            $this->cacheManager->flush();
        } catch (ExceptionCollection $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
