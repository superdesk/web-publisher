<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
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
        $crawler = $client->request('GET', '/news/features');

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

    public function testLoadingWhenCollectionRouteHasNoTemplate()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadCollectionRouteArticles',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $client->request('GET', '/collection-no-template');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testLoadingCollectionRouteWithArticles()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadCollectionRouteArticles',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $crawler = $client->request('GET', '/collection-test');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("collection.html.twig")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Test art1")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Test art2")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Test art3")')->count());

        $this->assertEquals(3, $crawler->filter('li.route-loaded-from-context')->count());
    }

    public function testLoadingFakeArticleOnCollectionRoute()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadCollectionRouteArticles',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $crawler = $client->request('GET', '/collection-test/fake-article');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testLoadingArticlesOnCollectionRoute()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadCollectionRouteArticles',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $crawler = $client->request('GET', '/collection-test/test-art1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("Test art1")')->count());

        $crawler = $client->request('GET', '/collection-test/test-art2');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("Test art2")')->count());

        $crawler = $client->request('GET', '/collection-test/test-art3');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("Test art3")')->count());
    }

    public function testLoadingCollectionRouteWithContentAssignedAndNoTemplate()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadCollectionRouteArticles',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $client->enableProfiler();
        $client->request('GET', '/collection-content');
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
                'parent' => '',
                'template_name' => 'test.html.twig',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"id":"\/swp\/123456\/123abc\/routes\/simple-test-route","content":null,"static_prefix":null,"variable_pattern":null,"name":"simple-test-route","children":[],"id_prefix":"\/swp\/123456\/123abc\/routes","template_name":"test.html.twig","articles_template_name":null,"type":"content","cache_time_in_seconds":0,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/\/simple-test-route"}}}', $client->getResponse()->getContent());

        $crawler = $client->request('GET', '/simple-test-route');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Check that route id is in the rendered html - accessed through {% gimme.route.id %}
        $this->assertTrue($crawler->filter('html:contains("/swp/123456/123abc/routes/simple-test-route")')->count() === 1);
    }

    public function testTestLoadingRouteWithCustomArticlesTemplate()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadCollectionRouteArticles',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $crawler = $client->request('GET', '/collection-content/some-other-content');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("theme_test/test.html.twig")')->count());
    }
}
