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
use SWP\Bundle\CoreBundle\Util\MimeTypeHelper;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Class MimeTypeListener.
 */
class MimeTypeListener
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
        if (null !== $routeObject) {
            $extension = pathinfo($routeObject->getStaticPrefix().$routeObject->getVariablePattern(), PATHINFO_EXTENSION);
            $response = $event->getResponse();
            if ('' !== $extension && Response::HTTP_OK === $response->getStatusCode()) {
                //check if data are transferred through the route
                if (preg_match('/[{}]/', $extension)) {
                    // check if any twig parameters match found or not
                    if (false !== preg_match('/{(.*?)}/', $extension, $matches)) {
                        $extension = $event->getRequest()->get($matches[1]);
                    }
                }
                $mim = MimeTypeHelper::getByExtension($extension);
                $response->headers->set('Content-Type', $mim .'; charset=UTF-8');
            }
        }
    }
}
