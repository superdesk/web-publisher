<?php
namespace SWP\WebRendererBundle\Tests\Routing\Loader;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\WebRendererBundle\Routing\Loader\PagesLoader;

class PagesLoaderTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = $this->getContainer()->get('doctrine')->getManager();
    }

    public function testPagesLoaderWithoutPages()
    {
        $this->loadFixtures([]);

        $pagesLoader = new PagesLoader($this->em);
        $routes = $pagesLoader->load('.');
        $this->assertCount(0, $routes);
    }

    public function testPagesLoaderWithPages()
    {
        $this->loadFixtures([
            'SWP\WebRendererBundle\Tests\Fixtures\ORM\LoadPagesData'
        ]);

        $pagesLoader = new PagesLoader($this->em);
        $routes = $pagesLoader->load('.');
        $this->assertCount(1, $routes);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}