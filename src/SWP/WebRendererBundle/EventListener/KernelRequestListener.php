<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\WebRendererBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class KernelRequestListener
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->isMasterRequest() && $event->getRequest()->attributes->has('page_id')) {
            // Notify listeners (eg. RoutePageListener) about Page associated to requested route
            $this->eventDispatcher->dispatch('swp.context.page', new GenericEvent(null, [
                'pageId' => $event->getRequest()->attributes->get('page_id'),
                'route_name' => $event->getRequest()->attributes->get('_route'),
            ]));
        }

        return;
    }
}
