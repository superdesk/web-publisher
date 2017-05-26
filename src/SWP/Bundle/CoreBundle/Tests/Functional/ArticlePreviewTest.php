<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Functional;

use SWP\Bundle\FixturesBundle\WebTestCase;

final class ArticlePreviewTest extends WebTestCase
{
    private $router;

    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/article_preview.yml',
        ], true);

        $this->router = $this->getContainer()->get('router');
    }

    public function testArticlePreview()
    {
        $route = $this->createRoute();

        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $crawler = $client->request('GET', $this->router->generate(
            'swp_article_preview',
            ['routeId' => $route['id'], 'slug' => 'art1-not-published', 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Slug: art1-not-published")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Current tenant: Default tenant")')->count());
    }

    public function testArticlePreviewWithoutToken()
    {
        $route = $this->createRoute();
        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_article_preview',
            ['routeId' => $route['id'], 'slug' => 'art1-not-published']
        ));

        self::assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testArticlePreviewWithFakeToken()
    {
        $route = $this->createRoute();

        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_article_preview',
            ['routeId' => $route['id'], 'slug' => 'art1-not-published', 'auth_token' => 'fake']
        ));

        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testArticlePreviewWithNotExistingArticle()
    {
        $route = $this->createRoute();

        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_article_preview',
            ['routeId' => $route['id'], 'slug' => 'fake-article', 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertFalse($client->getResponse()->isSuccessful());
        self::assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    public function testArticlePreviewWithNotExistingRoute()
    {
        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_article_preview',
            ['routeId' => 9999, 'slug' => 'art1-not-published', 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertFalse($client->getResponse()->isSuccessful());
        self::assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    public function testArticlePreviewWithoutRouteTemplate()
    {
        $route = $this->createRouteWithoutTemplate();

        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_article_preview',
            ['routeId' => $route['id'], 'slug' => 'art1-not-published', 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertFalse($client->getResponse()->isSuccessful());
        self::assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    public function testTenantsFromDifferentOrganizationsCantPreviewArticlesOfEachother()
    {
        $route = $this->createRoute();
        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $crawler = $client->request('GET', $this->router->generate(
            'swp_article_preview',
            ['routeId' => $route['id'], 'slug' => 'art1-not-published', 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Slug: art1-not-published")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Current tenant: Default tenant")')->count());

        $client->request('GET', $this->router->generate(
            'swp_article_preview',
            ['routeId' => $route['id'], 'slug' => 'art1-not-published', 'auth_token' => base64_encode('client1_token')]
        ));

        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testOneTenantCanPreviewArticlesOfOtherTenantsUsingTokensWithinSameOrganization()
    {
        $route = $this->createRoute();
        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $crawler = $client->request('GET', $this->router->generate(
            'swp_article_preview',
            ['routeId' => $route['id'], 'slug' => 'art1-not-published', 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Slug: art1-not-published")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Current tenant: Default tenant")')->count());

        $client->request('GET', $this->router->generate(
            'swp_article_preview',
            ['routeId' => $route['id'], 'slug' => 'art1-not-published', 'auth_token' => base64_encode('client2_token')]
        ));

        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Slug: art1-not-published")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Current tenant: Default tenant")')->count());
    }

    private function createRoute()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'news',
                'type' => 'collection',
                'content' => null,
                'templateName' => 'news.html.twig',
                'articlesTemplateName' => 'article.html.twig',
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }

    private function ensureArticleIsNotAccessible()
    {
        $client = static::createClient();
        $client->request('GET', '/news/art1-not-published');

        self::assertFalse($client->getResponse()->isSuccessful());
        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }

    private function createRouteWithoutTemplate()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'news',
                'type' => 'collection',
                'content' => null,
                'templateName' => 'news.html.twig',
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }
}
