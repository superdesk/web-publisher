<?php

namespace SWP\WebRendererBundle\Tests\EventListener;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\WebRendererBundle\EventListener\KernelRequestListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

class KernelRequestListenerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
    }

    public function testEmitingContextPageEvent()
    {
        $masterRequest = Request::create('/about-us', 'GET');
        $masterRequest->attributes->set('page_id', 1);
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');

        $kernelRequestListener = new KernelRequestListener($eventDispatcher);
        $getResponseEvent = new \Symfony\Component\HttpKernel\Event\GetResponseEvent(
            $this->getContainer()->get('kernel'), $masterRequest, HttpKernelInterface::MASTER_REQUEST
        );

        $kernelRequestListener->onKernelRequest($getResponseEvent);
        $this->assertTrue(array_key_exists(
            'swp.context.page.SWP\WebRendererBundle\EventListener\RoutePageListener::onRoutePage',
            $eventDispatcher->getCalledListeners()
        ));
    }
}
