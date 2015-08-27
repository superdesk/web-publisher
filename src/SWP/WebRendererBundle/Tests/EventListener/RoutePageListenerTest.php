<?php

namespace SWP\WebRendererBundle\Tests\EventListener;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\WebRendererBundle\EventListener\RoutePageListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\GenericEvent;


class RoutePageListenerTest extends WebTestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
    }

    public function testPagesLoaderWithoutPages()
    {
        $this->loadFixtures([
            'SWP\WebRendererBundle\Tests\Fixtures\ORM\LoadPagesData'
        ]);

        $registryManager = $this->getContainer()->get('doctrine');
        $context = $this->getContainer()->get('swp_template_engine_context');
        $genericEvent =

        $kernelRequestListener = new RoutePageListener($registryManager, $context);
        $getResponseEvent = new GenericEvent(null, [
            'pageId' => 1,
            'route_name' => 'swp_page_about',
        ]);

        $kernelRequestListener->onRoutePage($getResponseEvent);

        $this->assertInternalType('array', $context->getCurrentPage());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->getContainer()->get('doctrine')->getManager()->close();
    }
}
