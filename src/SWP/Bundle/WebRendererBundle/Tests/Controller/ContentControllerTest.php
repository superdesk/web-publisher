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
namespace SWP\Bundle\WebRendererBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class ContentControllerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/tenant.yml',
        ]);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);
    }

    public function testLoadingContainerPageArticle()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $crawler = $client->request('GET', '/articles/features');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Features")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Content:")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Id: /swp/default/content/features")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Current tenant: default")')->count() === 1);
    }

    public function testLoadingNotExistingArticleUnderContainerPage()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $client->request('GET', '/news/featuress');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testTestLoadingRouteWithCustomTemplate()
    {
        $router = $this->getContainer()->get('router');
        $client = static::createClient();
        $client->enableProfiler();
        $client->request('POST', $router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'parent' => '/',
                'template_name' => 'test.html.twig',
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"id":"\/swp\/default\/routes\/simple-test-route","content":null,"static_prefix":null,"variable_pattern":null,"name":"simple-test-route","children":[],"id_prefix":"\/swp\/default\/routes","template_name":"test.html.twig","type":"content","_links":{"self":{"href":"\/api\/v1\/content\/routes\/\/simple-test-route"}}}', $client->getResponse()->getContent());

        $client->enableProfiler();
        $crawler = $client->request('GET', '/simple-test-route');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("theme_test/test.html.twig")')->count() === 1);
    }
}
