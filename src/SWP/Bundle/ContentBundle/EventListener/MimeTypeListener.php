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

use Hoa\Mime\Mime;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class MimeTypeListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        /** @var RouteInterface $routeObject */
        $routeObject = $event->getRequest()->get(DynamicRouter::ROUTE_KEY);

        if (null !== $routeObject) {
            $extension = pathinfo($routeObject->getName(), PATHINFO_EXTENSION);
            $response = $event->getResponse();
            if ('' !== $extension && Response::HTTP_OK === $response->getStatusCode()) {
                $response->headers->set('Content-Type', Mime::getMimeFromExtension($extension).'; charset=UTF-8');
            }
        }
    }
}
