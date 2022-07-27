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

final class PackagePreviewTest extends WebTestCase
{
    const TEST_ITEM_CONTENT = <<<'EOD'
    {
      "language": "en",
      "byline":"John Jones",
      "source":"Sourcefabric",
      "type":"text",
      "description_text":"Lorem ipsum abstract",
      "guid":"urn:newsml:sd-master.test.superdesk.org:2018-01-18T09:26:52.402693:f0d01867-e91e-487e-9a50-b638b78fc4bc",
      "profile":"Article",
      "wordcount":3,
      "urgency":3,
      "authors":[
        {
          "biography":"bioquil",
          "name":"Nareg Asmarian",
          "jobtitle":{
            "qcode":"1",
            "name":"quality check"
          },
          "role":"writer"
        },
        {
          "biography":"not dead yet",
          "name":"vincer vincer",
          "role":"subeditor"
        }
      ],
      "copyrightholder":"",
      "slugline":"art1-not-published",
      "headline":"testing authors",
      "version":"3",
      "description_html":"<p>Lorem ipsum abstract</p>",
      "located":"Prague",
      "pubstatus":"usable",
      "copyrightnotice":"",
      "body_html":"<p>Lorem ipsum body</p>",
      "usageterms":"",
      "priority":6,
      "versioncreated":"2018-01-18T09:31:58+0000",
      "firstpublished":"2018-01-18T09:31:58+0000",
      "charcount":16,
      "service":[
        {
          "code":"f",
          "name":"sports"
        }
      ],
      "readtime":0,
      "firstcreated":"2018-01-18T09:26:52+0000"
    }
EOD;

    private $router;

    public function setUp(): void
    {
        parent::setUp();;
        $this->loadCustomFixtures(['tenant']);
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/package_preview.yml',
        ], true);

        $this->router = $this->getContainer()->get('router');
    }

    public function testPackagePreview()
    {
        $route = $this->createRoute();

        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT
        );

        $client->enableProfiler();
        $crawler = $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => $route['id'], 'id' => 2, 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Slug: art1-not-published")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Current tenant: Default tenant")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Name: Nareg Asmarian")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Slug: nareg-asmarian")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Biography: bio")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Name: vincer vincer")')->count());
    }

    public function testPackagePreviewWithAmp()
    {
        $route = $this->createRoute();

        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => $route['id'], 'id' => 1, 'auth_token' => base64_encode('test_token:'), 'amp' => true]
        ));

        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertContains('<script async src="https://cdn.ampproject.org/v0.js"></script>', $client->getResponse()->getContent());
        self::assertContains('art1 not published', $client->getResponse()->getContent());
    }

    public function testPackagePreviewWithoutToken()
    {
        $route = $this->createRoute();
        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => $route['id'], 'id' => 1]
        ));

        self::assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testPackagePreviewWithFakeToken()
    {
        $route = $this->createRoute();

        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => $route['id'], 'id' => 1, 'auth_token' => 'fake']
        ));

        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testPackagePreviewWithNotExistingPackage()
    {
        $route = $this->createRoute();

        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => $route['id'], 'id' => 9999, 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertFalse($client->getResponse()->isSuccessful());
        self::assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    public function testPackagePreviewWithNotExistingRoute()
    {
        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => 9999, 'id' => 1, 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertFalse($client->getResponse()->isSuccessful());
        self::assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    public function testPackagePreviewWithoutRouteTemplate()
    {
        $route = $this->createRouteWithoutTemplate();

        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => $route['id'], 'id' => 1, 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertFalse($client->getResponse()->isSuccessful());
        self::assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    public function testPackagePreviewWithNotExistingRouteTemplate()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'news',
                'type' => 'collection',
                'content' => null,
                'articlesTemplateName' => 'fake.html.twig',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $route = json_decode($client->getResponse()->getContent(), true);

        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => $route['id'], 'id' => 1, 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertFalse($client->getResponse()->isSuccessful());
        self::assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    public function testTenantsFromDifferentOrganizationsCantPreviewArticlesOfEachOther()
    {
        $route = $this->createRoute();
        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $crawler = $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => $route['id'], 'id' => 1, 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Slug: art1-not-published")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Current tenant: Default tenant")')->count());

        $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => $route['id'], 'id' => 1, 'auth_token' => base64_encode('client1_token')]
        ));

        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testOneTenantCanPreviewPackagesOfOtherTenantsUsingTokensWithinSameOrganization()
    {
        $route = $this->createRoute();
        $this->ensureArticleIsNotAccessible();

        $client = static::createClient([], ['HTTP_Authorization' => null]);
        $crawler = $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => $route['id'], 'id' => 1, 'auth_token' => base64_encode('test_token:')]
        ));

        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Slug: art1-not-published")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Current tenant: Default tenant")')->count());

        $client->request('GET', $this->router->generate(
            'swp_package_preview',
            ['routeId' => $route['id'], 'id' => 1, 'auth_token' => base64_encode('client2_token')]
        ));

        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Slug: art1-not-published")')->count());
        self::assertGreaterThan(0, $crawler->filter('html:contains("Current tenant: Default tenant")')->count());
    }

    private function createRoute()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'news',
                'type' => 'collection',
                'content' => null,
                'templateName' => 'news.html.twig',
                'articlesTemplateName' => 'article.html.twig',
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
                'name' => 'news',
                'type' => 'collection',
                'content' => null,
                'templateName' => 'news.html.twig',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }
}
