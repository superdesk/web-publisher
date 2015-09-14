<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\WebRendererBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use SWP\ContentBundle\Document\Article;

class ContentControllerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/DataFixtures/ORM/Test/page.yml',
        ]);

        $this->runCommand('doctrine:phpcr:init:dbal', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);
    }

    public static function setUpBeforeClass()
    {
        $filesystem = new Filesystem();
        $filesystem->mirror(__DIR__.'/../Fixtures/theme_1', __DIR__.'/../../../../../app/Resources/themes/theme_test');
    }

    public static function tearDownAfterClass()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__.'/../../../../../app/Resources/themes/theme_test');
    }

    public function testLoadingAboutUsPage()
    {
        $manager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $article = new Article();
        $article->setTitle('About us');
        $article->setContent('Lorem ipsum');
        $manager->persist($article);
        $manager->flush();

        $this->assertTrue($article->getTitle() === 'About us');

        $client = static::createClient();
        $crawler = $client->request('GET', '/about-us');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("About us")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Lorem ipsum")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Id: /swp/content/about-us")')->count() === 1);
    }
}
