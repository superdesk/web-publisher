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

        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:phpcr:init:dbal', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('theme:setup', ['--env' => 'test', '--force' => true, 'name' => 'theme_test'], true);
    }

    public static function tearDownAfterClass()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__.'/../../../../../app/Resources/themes/theme_test');
    }

    public function testLoadingContainerPageArticle()
    {
        $this->loadFixtures([
            'SWP\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $crawler = $client->request('GET', '/articles/features');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Features")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Content:")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Id: /swp/content/features")')->count() === 1);
    }

    public function testLoadingNotExistingArticleUnderContainerPage()
    {
        $manager = $this->getContainer()->get('doctrine_phpcr.odm.document_manager');

        $client = static::createClient();
        $crawler = $client->request('GET', '/news/featuress');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
