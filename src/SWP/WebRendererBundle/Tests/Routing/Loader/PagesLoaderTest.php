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
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->pagesLoader = new PagesLoader($this->getContainer()->get('doctrine'));
    }

    public function testPagesLoader()
    {
        $this->loadFixtureFiles([]);
        $this->assertCount(0, $this->pagesLoader->load('.'));

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/DataFixtures/ORM/Test/page.yml',
            '@SWPFixturesBundle/DataFixtures/ORM/Test/pagecontent.yml',
        ]);

        $this->assertTrue(count($this->pagesLoader->load('.')) > 0);

        $this->assertTrue($this->pagesLoader->supports('.', 'pages'));
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
