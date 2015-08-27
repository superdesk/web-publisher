<?php
namespace SWP\WebRendererBundle\Tests\Routing\Loader;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\WebRendererBundle\Routing\Loader\PagesLoader;

class PagesLoaderTest extends WebTestCase
{
    /**
     * @var \SWP\WebRendererBundle\Routing\Loader\PagesLoader
     */
    private $pagesLoader;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->pagesLoader = new PagesLoader($this->getContainer()->get('doctrine'));
    }

    public function testPagesLoaderWithoutPages()
    {
        $this->loadFixtures([]);
        $this->assertCount(0, $this->pagesLoader->load('.'));

        $this->loadFixtures([
            'SWP\WebRendererBundle\Tests\Fixtures\ORM\LoadPagesData'
        ]);
        $this->assertCount(1, $this->pagesLoader->load('.'));
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
