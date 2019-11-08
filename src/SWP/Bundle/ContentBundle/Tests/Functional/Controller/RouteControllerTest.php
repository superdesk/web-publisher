<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Functional\Controller;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class RouteControllerTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->initDatabase();
        $this->router = $this->getContainer()->get('router');
    }

    public function testCreateEmptyContentRoutesApi()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'simple-test-route',
                'type' => 'content',
                'description' => 'simple route description',
                'content' => null,
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertEquals(
            json_decode(
                '{"id":1,"content":null,"static_prefix":"\/simple-test-route","variable_pattern":null,"children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":0,"name":"simple-test-route","position":0,"parent":null,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/1"}}, "slug":"simple-test-route", "requirements":[], "lft":1, "rgt": 2, "description":"simple route description"}',
                true
            ),
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testCreateContentRoutesApi()
    {
        $this->loadFixtureFiles(
            ['@SWPContentBundle/Tests/Functional/app/Resources/fixtures/separate_article.yml'],
            'default'
        );

        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => 2,
                'cacheTimeInSeconds' => 1,
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArraySubset(
            json_decode(
                '{"id":2,"content":{"id":2,"title":"Test content article","body":"Test article content","slug":"test-content-article","status":"published","route":{"id":1,"content":null,"static_prefix":null,"variable_pattern":"\/{slug}","children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"news","position":0,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/1"}}},"template_name":null,"publish_start_date":null,"publish_end_date":null,"is_publishable":true,"metadata":null,"media":[],"lead":null,"keywords":[],"_links":{"self":{"href":"\/api\/v2\/content\/articles\/test-content-article"},"online":{"href":"\/test-content-article"}}},"static_prefix":"\/simple-test-route","variable_pattern":null,"children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"simple-test-route","position":1,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/2"}}}',
                true
            ),
            $content
        );
    }

    public function testCreateAndUpdateRoutesApi()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => null,
                'cacheTimeInSeconds' => 1,
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            json_decode(
                '{"id":1,"content":null,"static_prefix":"\/simple-test-route","variable_pattern":null,"children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"simple-test-route","position":0,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/1"}}}',
                true
            ),
            json_decode($client->getResponse()->getContent(), true)
        );

        $client->request(
            'PATCH',
            $this->router->generate('swp_api_content_update_routes', ['id' => 1]),
            [
                'name' => 'simple-test-route-new-name',
            ]
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['name' => 'simple-test-route-new-name'],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testCreateAndUpdateAndDeleteRoutesApi()
    {
        $this->loadFixtureFiles(
            [
                '@SWPContentBundle/Tests/Functional/app/Resources/fixtures/separate_article.yml',
            ],
            'default'
        );

        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => null,
                'cacheTimeInSeconds' => 1,
            ]
        );

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'PATCH',
            $this->router->generate('swp_api_content_update_routes', ['id' => $content['id']]),
            [
                'name' => 'simple-edited-test-route',
                'type' => 'collection',
                'content' => 2,
                'cacheTimeInSeconds' => 50,
            ]
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            json_decode(
                '{"content":{"title":"Test content article","body":"Test article content","slug":"test-content-article","status":"published","route":{"content":null,"static_prefix":null,"variable_pattern":"\/{slug}","children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"news","position":0},"template_name":null,"publish_start_date":null,"publish_end_date":null,"is_publishable":true,"metadata":null,"media":[],"lead":null},"static_prefix":"\/simple-test-route","variable_pattern":"\/{slug}","children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":50,"name":"simple-edited-test-route","position":1}',
                true
            ),
            json_decode($client->getResponse()->getContent(), true)
        );

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => $content['id']]));
        self::assertEquals(409, $client->getResponse()->getStatusCode());

        $client->request(
            'PATCH',
            $this->router->generate('swp_api_content_update_routes', ['id' => $content['id']]),
            [
                'content' => null,
            ]
        );
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => $content['id']]));
        self::assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testWithCustomTemplatesRoutesApi()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'simple-test-route',
                'type' => 'content',
                'templateName' => 'test.html.twig',
                'cacheTimeInSeconds' => 1,
            ]
        );
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            json_decode(
                '{"content":null,"static_prefix":"\/simple-test-route","variable_pattern":null,"children":[],"level":0,"template_name":"test.html.twig","articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"simple-test-route","position":0}',
                true
            ),
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testSettingNotSupportedRouteType()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'testing-route-type',
                'type' => 'fake-type',
                'templateName' => 'test.html.twig',
                'cacheTimeInSeconds' => 1,
            ]
        );
        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            json_decode(
                '{"code":400,"message":"Validation Failed","errors":{"children":{"name":{},"type":{"errors":["The type \"fake-type\" is not allowed. Supported types are: \"collection, content, custom\"."]}}}}',
                true
            ),
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testRemovingParent()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'root',
                'type' => 'collection',
            ]
        );

        $rootContent = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'root-child1',
                'type' => 'collection',
                'parent' => $rootContent['id'],
            ]
        );

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertEquals(1, $content['level']);

        $client->request(
            'PATCH',
            $this->router->generate('swp_api_content_update_routes', ['id' => $content['id']]),
            [
                'parent' => null,
            ]
        );

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals(0, $content['level']);
    }

    public function testNestedRoutes()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'root',
                'type' => 'collection',
            ]
        );

        $rootContent = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'root-child1',
                'type' => 'collection',
                'parent' => $rootContent['id'],
            ]
        );

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertEquals(1, $content['level']);

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'child1-root-child1',
                'type' => 'collection',
                'parent' => $content['id'],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_routes', ['id' => $rootContent['id']]));
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        self::assertArraySubset(
            json_decode(
                '{"id":1,"content":null,"static_prefix":"\/root","variable_pattern":"\/{slug}","parent":null,"children":[{"id":2,"content":null,"static_prefix":"\/root\/root-child1","variable_pattern":"\/{slug}","parent":1,"children":[{"id":3,"content":null,"static_prefix":"\/root\/root-child1\/child1-root-child1","variable_pattern":"\/{slug}","parent":2,"children":[],"level":2,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"child1-root-child1","slug":"child1-root-child1","position":0,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/3"},"parent":{"href":"\/api\/v2\/content\/routes\/2"}}}],"level":1,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"root-child1","slug":"root-child1","position":0,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/2"},"parent":{"href":"\/api\/v2\/content\/routes\/1"}}}],"level":0,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"root","slug":"root","position":0,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/1"}}}',
                true
            ),
            $content
        );

        $client->request(
            'DELETE',
            $this->router->generate('swp_api_content_delete_routes', ['id' => $rootContent['id']])
        );
        self::assertEquals(409, $client->getResponse()->getStatusCode());
    }

    public function testAssigningNotExistingRoute()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'root',
                'type' => 'collection',
                'parent' => 99999,
            ]
        );

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            json_decode(
                '{"message":"Validation Failed","errors":{"children":{"parent":{"errors":["The selected route does not exist!"]}}}}',
                true
            ),
            $content
        );
    }

    public function testFilterRoutesByType()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'route1',
                'type' => 'content',
                'cacheTimeInSeconds' => 1,
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_create_routes'),
            [
                'name' => 'route2',
                'type' => 'collection',
                'cacheTimeInSeconds' => 2,
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_create_routes'));

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(
            json_decode(
                '{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/?page=1&limit=10"},"first":{"href":"\/api\/v2\/content\/routes\/?page=1&limit=10"},"last":{"href":"\/api\/v2\/content\/routes\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"content":null,"static_prefix":"\/route1","variable_pattern":null,"children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"route1","position":0,"parent":null,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/1"}},"slug":"route1","requirements":[], "lft":1, "rgt": 2, "description": null},{"id":2,"content":null,"static_prefix":"\/route2","variable_pattern":"\/{slug}","children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":2,"name":"route2","position":1,"parent":null,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/2"}},"slug":"route2","requirements":{"slug":"[a-zA-Z0-9*\\\-_]+"},"lft":3,"rgt": 4,"description": null}]}}',
                true
            ),
            $content
        );

        $client->request(
            'GET',
            $this->router->generate(
                'swp_api_content_create_routes',
                [
                    'type' => 'content',
                ]
            )
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(
            json_decode(
                '{"page":1,"limit":10,"pages":1,"total":1,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/?type=content&page=1&limit=10"},"first":{"href":"\/api\/v2\/content\/routes\/?type=content&page=1&limit=10"},"last":{"href":"\/api\/v2\/content\/routes\/?type=content&page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"content":null,"static_prefix":"\/route1","variable_pattern":null,"children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"route1","position":0,"parent":null,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/1"}},"slug":"route1","requirements":[],"lft":1,"rgt":2, "description": null}]}}',
                true
            ),
            $content
        );

        $client->request(
            'GET',
            $this->router->generate(
                'swp_api_content_create_routes',
                [
                    'type' => 'collection',
                ]
            )
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(
            json_decode(
                '{"page":1,"limit":10,"pages":1,"total":1,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/?type=collection&page=1&limit=10"},"first":{"href":"\/api\/v2\/content\/routes\/?type=collection&page=1&limit=10"},"last":{"href":"\/api\/v2\/content\/routes\/?type=collection&page=1&limit=10"}},"_embedded":{"_items":[{"id":2,"content":null,"static_prefix":"\/route2","variable_pattern":"\/{slug}","children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":2,"name":"route2","position":1,"parent":null,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/2"}},"slug":"route2","requirements":{"slug":"[a-zA-Z0-9*\\\-_]+"},"lft":3,"rgt":4,"description":null}]}}',
                true
            ),
            $content
        );

        $client->request(
            'GET',
            $this->router->generate(
                'swp_api_content_create_routes',
                [
                    'type' => 'fake',
                ]
            )
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(
            json_decode(
                '{"page":1,"limit":10,"pages":1,"total":0,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/?type=fake&page=1&limit=10"},"first":{"href":"\/api\/v2\/content\/routes\/?type=fake&page=1&limit=10"},"last":{"href":"\/api\/v2\/content\/routes\/?type=fake&page=1&limit=10"}},"_embedded":{"_items":[]}}',
                true
            ),
            $content
        );
    }

    public function testRemoveParentRoute()
    {
        $this->loadFixtures(
            [
                'SWP\Bundle\ContentBundle\Tests\Functional\app\Resources\fixtures\LoadArticlesData',
            ], 'default'
        );

        $client = static::createClient();
        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => 2]));
        self::assertEquals(409, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_routes', ['id' => 4]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_articles', ['id' => 8]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(ArticleInterface::STATUS_PUBLISHED, $content['status']);

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => 4]));
        self::assertEquals(204, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_articles', ['id' => 8]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(ArticleInterface::STATUS_NEW, $content['status']);
    }
}
