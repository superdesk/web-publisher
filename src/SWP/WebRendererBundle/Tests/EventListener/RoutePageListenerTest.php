<?php

namespace SWP\WebRendererBundle\Tests\EventListener;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\WebRendererBundle\EventListener\RoutePageListener;
use Symfony\Component\EventDispatcher\GenericEvent;

class RoutePageListenerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
    }

    public function testPagesLoaderWithoutPages()
    {
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/DataFixtures/ORM/Test/page.yml',
            '@SWPFixturesBundle/DataFixtures/ORM/Test/pagecontent.yml',
        ]);

        $registryManager = $this->getContainer()->get('doctrine');
        $context = $this->getContainer()->get('swp_template_engine_context');

        $kernelRequestListener = new RoutePageListener($registryManager, $context);
        $genericEvent = new GenericEvent(null, [
            'pageId' => 1,
            'route_name' => 'swp_page_about_us',
        ]);

        $kernelRequestListener->onRoutePage($genericEvent);

        $this->assertInternalType('array', $context->getCurrentPage());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->getContainer()->get('doctrine')->getManager()->close();
    }
}
