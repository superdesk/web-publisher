<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\EventListener;

use DG\BypassFinals;
use FOS\RestBundle\View\ViewHandler;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use PHPUnit\Framework\TestCase;
use SWP\Component\Common\EventListener\ResourceResponseListener;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class ResourceResponseListenerTest extends TestCase
{
    public function testInitialization()
    {
        $viewHandler = $this->createMock(ViewHandler::class);
        $listener = new ResourceResponseListener($viewHandler);

        self::assertInstanceOf(ResourceResponseListener::class, $listener);
    }

    public function testOnKernelView()
    {
        $viewHandler = $this->createMock(ViewHandler::class);
        $viewHandler
            ->method('handle')
            ->willReturn(new Response());

        $listener = new ResourceResponseListener($viewHandler);

        $pagination = new SlidingPagination([]);
        $pagination->setItems([]);
        $pagination->setTotalItemCount(0);
        $pagination->setCurrentPageNumber(1);
        $pagination->setItemNumberPerPage(10);
        $resourcesListResponse = new ResourcesListResponse($pagination);
        $event = $this->createMock(ViewEvent::class);
        $event
            ->expects($this->once())
            ->method('getControllerResult')
            ->willReturn($resourcesListResponse);
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn(new Request());

        $listener->onKernelView($event);

        $singleResponse = new SingleResourceResponse([1, 2, 3]);
        $event = $this->createMock(ViewEvent::class);
        $event
            ->expects($this->once())
            ->method('getControllerResult')
            ->willReturn($singleResponse);
        $event
            ->expects($this->never())
            ->method('getRequest')
            ->willReturn(new Request());

        $listener->onKernelView($event);
    }
}
