<?php

/*
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
        $this->loadCustomFixtures(['tenant', 'article']);

        $client = static::createClient();
        $crawler = $client->request('GET', '/news/features');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Features")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Content:")')->count() === 1);
        $this->assertTrue($crawler->filter('html:contains("Current tenant: Default tenant")')->count() === 1);
    }

    public function testLoadingNotExistingArticleUnderContainerPage()
    {
        $this->loadCustomFixtures(['tenant', 'article']);

        $client = static::createClient();
        $client->request('GET', '/news/featuress');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testLoadingWhenCollectionRouteHasNoTemplate()
    {
        $this->loadCustomFixtures(['tenant', 'collection_route']);

        $client = static::createClient();
        $client->request('GET', '/collection-no-template');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testLoadingCollectionRouteWithArticles()
    {
        $this->loadCustomFixtures(['tenant', 'collection_route']);

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
        $this->loadCustomFixtures(['tenant', 'collection_route']);

        $client = static::createClient();
        $client->request('GET', '/collection-test/fake-article');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testLoadingArticlesOnCollectionRoute()
    {
        $this->loadCustomFixtures(['tenant', 'collection_route']);

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
        $this->loadCustomFixtures(['tenant', 'collection_route']);

        $client = static::createClient();

        $client->request('GET', '/collection-content');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testTestLoadingRouteWithCustomTemplate()
    {
        $this->loadCustomFixtures(['tenant']);

        $router = $this->getContainer()->get('router');
        $client = static::createClient();

        $client->request('POST', $router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => null,
                'templateName' => 'test.html.twig',
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"id":3,"content":null,"staticPrefix":"\/simple-test-route","variablePattern":null,"root":3,"parent":null,"children":[],"level":0,"templateName":"test.html.twig","articlesTemplateName":null,"type":"content","cacheTimeInSeconds":0,"name":"simple-test-route","position":1,"articlesCount":0,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/3"}}}', $client->getResponse()->getContent());

        $crawler = $client->request('GET', '/simple-test-route');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Check that route id is in the rendered html - accessed through {% gimme.route.id %}
        $this->assertTrue($crawler->filter('html:contains("3")')->count() === 1);
    }

    public function testTestLoadingRouteWithCustomArticlesTemplate()
    {
        $this->loadCustomFixtures(['tenant', 'collection_route']);

        $client = static::createClient();
        $crawler = $client->request('GET', '/collection-content/some-other-content');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('html:contains("theme_test/test.html.twig")')->count());
    }

    public function testRouteWithExtension()
    {
        $this->loadCustomFixtures(['tenant']);
        $client = static::createClient();
        $router = $this->getContainer()->get('router');
        $client->request('POST', $router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'feed/sitemap.rss',
                'type' => 'content',
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', '/feed/sitemap.rss');

        self::assertEquals('application/rss+xml; charset=UTF-8', $client->getResponse()->headers->get('Content-Type'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
