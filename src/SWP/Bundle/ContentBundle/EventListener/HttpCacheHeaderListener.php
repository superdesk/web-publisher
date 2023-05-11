<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class HttpCacheHeaderListener
{
    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        /** @var RouteInterface $routeObject */
        $routeObject = $event->getRequest()->get(DynamicRouter::ROUTE_KEY);

        if ($routeObject instanceof RouteInterface) {
            // Get expiry time
            $cacheTimeInSeconds = $routeObject->getCacheTimeInSeconds();
            if (0 < $cacheTimeInSeconds) {
                $response = $event->getResponse();
                $response->setSharedMaxAge($cacheTimeInSeconds);
                $response->setPublic();
            }
        }
    }
}
