<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\DependencyInjection;

use SWP\Bundle\ContentBundle\EventListener\MimeTypeListener;
use SWP\Bundle\ContentBundle\Model\Route;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class MimeTypeListenerTest extends WebTestCase
{
    public function testHandlingRouteExtension()
    {
        $listener = new MimeTypeListener();
        $request = new Request();
        $route = new Route();

        $route->setName('feed/siteamap.rss');
        $request->attributes->set(DynamicRouter::ROUTE_KEY, $route);
        $event = new FilterResponseEvent(
            $this->getContainer()->get('kernel'),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );
        $listener->onKernelResponse($event);
        $eventResponse = $event->getResponse();
        self::assertEquals('application/rss+xml', $eventResponse->headers->get('Content-Type'));
        self::assertEquals(Response::HTTP_OK, $eventResponse->getStatusCode());

        $route->setName('articles');
        $request->attributes->set(DynamicRouter::ROUTE_KEY, $route);
        $event = new FilterResponseEvent(
            $this->getContainer()->get('kernel'),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );
        $listener->onKernelResponse($event);
        $eventResponse = $event->getResponse();
        self::assertEquals(null, $eventResponse->headers->get('Content-Type'));
        self::assertEquals(Response::HTTP_OK, $eventResponse->getStatusCode());
    }
}
