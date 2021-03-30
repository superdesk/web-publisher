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
        $this->assertTrue(1 === $crawler->filter('html:contains("Features")')->count());
        $this->assertTrue(1 === $crawler->filter('html:contains("Content:")')->count());
        $this->assertTrue(1 === $crawler->filter('html:contains("Current tenant: Default tenant")')->count());
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

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains('This is default "category.html.twig" template file.', $client->getResponse()->getContent());
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
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains('This is default "category.html.twig" template file.', $client->getResponse()->getContent());
    }

    public function testTestLoadingRouteWithCustomTemplate()
    {
        $this->loadCustomFixtures(['tenant']);

        $router = $this->getContainer()->get('router');
        $client = static::createClient();

        $client->request('POST', $router->generate('swp_api_content_create_routes'), [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => null,
                'templateName' => 'test.html.twig',
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"requirements":[],"id":3,"content":null,"static_prefix":"\/simple-test-route","variable_pattern":null,"parent":null,"children":[],"lft":3,"rgt":4,"level":0,"redirect_route":null,"template_name":"test.html.twig","articles_template_name":null,"type":"content","cache_time_in_seconds":0,"name":"simple-test-route","description":null,"slug":"simple-test-route","position":1,"articles_count":0,"paywall_secured":false,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/3"}}}', $client->getResponse()->getContent());

        $crawler = $client->request('GET', '/simple-test-route');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Check that route id is in the rendered html - accessed through {% gimme.route.id %}
        $this->assertTrue(1 === $crawler->filter('html:contains("3")')->count());
    }

    public function testLoadingArticlesOrderedByPageViews()
    {
        $this->loadCustomFixtures(['tenant', 'article']);

        $router = $this->getContainer()->get('router');
        $client = static::createClient();
        $client->request('PATCH', $router->generate('swp_api_content_update_routes', ['id' => 3]), [
                'templateName' => 'articles_by_pageviews.html.twig',
        ]);

        $expected = <<<'EOT'
Articles by page views count desc
    <a href="http://localhost/news/test-news-article">Test news article</a> Page views count: 20
    <a href="http://localhost/news/test-article">Test article</a> Page views count: 10
    <a href="http://localhost/news/features">Features</a> Page views count: 5

    <a href="http://localhost/news/features">Features</a>
    <a href="http://localhost/news/test-article">Test article</a>
    <a href="http://localhost/news/test-news-article">Test news article</a>

EOT;

        $client->request('GET', '/news');
        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertEquals($expected, $client->getResponse()->getContent());
    }

    public function testLoadingArticlesOrderedByPageViewsInRange()
    {
        $this->loadCustomFixtures(['tenant', 'article']);

        $router = $this->getContainer()->get('router');
        $client = static::createClient();
        $client->request('PATCH', $router->generate('swp_api_content_update_routes', ['id' => 3]), [
                'templateName' => 'articles_by_pageviews_in_date_range.html.twig',
        ]);

        $expected = <<<'EOT'
Articles by page views in last 7 days
    <a href="http://localhost/news/sports/test-news-sports-article">Test news sports article</a> Page views count: 30
    <a href="http://localhost/news/test-news-article">Test news article</a> Page views count: 20
    <a href="http://localhost/news/test-article">Test article</a> Page views count: 10
    <a href="http://localhost/news/features">Features</a> Page views count: 5
    <a href="http://localhost/articles-features?slug=features-client1">Features client1</a> Page views count: 0

Articles by page views in between 3 and 7 days ago
    <a href="http://localhost/articles-features?slug=features-client1">Features client1</a>  Page views count: 0

Articles by page views from yesterday
    <a href="http://localhost/news/sports/test-news-sports-article">Test news sports article</a>  Page views count: 30
    <a href="http://localhost/news/test-news-article">Test news article</a>  Page views count: 20
    <a href="http://localhost/news/test-article">Test article</a>  Page views count: 10
    <a href="http://localhost/news/features">Features</a>  Page views count: 5

EOT;

        $client->request('GET', '/news');
        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertEquals($expected, $client->getResponse()->getContent());
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
                'name' => 'Sitemap',
                'slug' => 'feed/sitemap.rss',
                'type' => 'content',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', '/feed/sitemap.rss');

        self::assertEquals('application/rss+xml; charset=UTF-8', $client->getResponse()->headers->get('Content-Type'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
