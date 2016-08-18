<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;

class ContentControllerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->initDatabase();
    }

    public function testLoadingContainerPageArticle()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $crawler = $client->request('GET', '/articles/features');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Features")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Content:")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Id: /swp/123456/123abc/content/features")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Current tenant: default")')->count() === 1);
    }

    public function testLoadingNotExistingArticleUnderContainerPage()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadArticlesData',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $client->request('GET', '/news/featuress');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testTestLoadingRouteWithCustomTemplate()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
        ], null, 'doctrine_phpcr');

        $router = $this->getContainer()->get('router');
        $client = static::createClient();
        $client->request('POST', $router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'parent' => '/',
                'template_name' => 'test.html.twig',
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"id":"\/swp\/123456\/123abc\/routes\/simple-test-route","content":null,"static_prefix":null,"variable_pattern":null,"name":"simple-test-route","children":[],"id_prefix":"\/swp\/123456\/123abc\/routes","template_name":"test.html.twig","type":"content","cache_time_in_seconds":0,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/\/simple-test-route"}}}', $client->getResponse()->getContent());

        $crawler = $client->request('GET', '/simple-test-route');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Check that route id is in the rendered html - accessed through {% gimme.route.id %}
        $this->assertTrue($crawler->filter('html:contains("/swp/123456/123abc/routes/simple-test-route")')->count() === 1);
    }
}
