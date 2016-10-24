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

namespace SWP\Bundle\TemplateEngineBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;
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
        $this->loadCustomFixtures(['tenant']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testCreateEmptyContentRoutesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => null,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertEquals(json_decode('{"id":3,"content":null,"static_prefix":"\/simple-test-route","variable_pattern":null,"parent":null,"children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":0,"name":"simple-test-route","position":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/3"}}}', true), json_decode($client->getResponse()->getContent(), true));
    }

    public function testCreateContentRoutesApi()
    {
        $this->loadCustomFixtures(['tenant', 'separate_article']);

        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => 2,
                'cacheTimeInSeconds' => 1,
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset(json_decode('{"content":{"title":"Test content article","body":"Test article content","slug":"test-content-article","status":"published","route":{"content":null,"static_prefix":null,"variable_pattern":"\/{slug}","parent":null,"children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"news","position":null},"template_name":null,"updated_at":null,"publish_start_date":null,"publish_end_date":null,"is_publishable":true,"metadata":null,"media":[],"lead":null,"_links":{"self":{"href":"\/api\/v1\/content\/articles\/test-content-article"},"online":{"href":"\/test-content-article"}}},"static_prefix":"\/simple-test-route","variable_pattern":null,"parent":null,"children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"simple-test-route","position":null}', true), $content);
    }

    public function testCreateAndUpdateRoutesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => null,
                'cacheTimeInSeconds' => 1,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":3,"content":null,"static_prefix":"\/simple-test-route","variable_pattern":null,"parent":null,"children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"simple-test-route","position":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/3"}}}', true), json_decode($client->getResponse()->getContent(), true));

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 1]), [
            'route' => [
                'name' => 'simple-test-route-new-name',
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['name' => 'simple-test-route-new-name'],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testCreateAndUpdateAndDeleteRoutesApi()
    {
        $this->loadCustomFixtures(['tenant', 'separate_article']);
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'content' => null,
                'cacheTimeInSeconds' => 1,
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => $content['id']]), [
            'route' => [
                'name' => 'simple-edited-test-route',
                'type' => 'collection',
                'content' => 2,
                'cacheTimeInSeconds' => 50,
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"content":{"title":"Test content article","body":"Test article content","slug":"test-content-article","status":"published","route":{"content":null,"static_prefix":null,"variable_pattern":"\/{slug}","parent":null,"children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"news","position":null},"template_name":null,"updated_at":null,"publish_start_date":null,"publish_end_date":null,"is_publishable":true,"metadata":null,"media":[],"lead":null},"static_prefix":"\/simple-edited-test-route","variable_pattern":"\/{slug}","parent":null,"children":[],"level":0,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":50,"name":"simple-edited-test-route","position":null}', true), json_decode($client->getResponse()->getContent(), true));

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => $content['id']]));
        self::assertEquals(409, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => $content['id']]), [
            'route' => [
                'content' => null,
            ],
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => $content['id']]));
        self::assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testWithCustomTemplatesRoutesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'templateName' => 'test.html.twig',
                'cacheTimeInSeconds' => 1,
            ],
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"content":null,"static_prefix":"\/simple-test-route","variable_pattern":null,"parent":null,"children":[],"level":0,"template_name":"test.html.twig","articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"simple-test-route","position":null}', true), json_decode($client->getResponse()->getContent(), true));
    }

    public function testSettingNotSupportedRouteType()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'testing-route-type',
                'type' => 'fake-type',
                'templateName' => 'test.html.twig',
                'cacheTimeInSeconds' => 1,
            ],
        ]);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"code":400,"message":"Validation Failed","errors":{"children":{"name":{},"type":{"errors":["The type \"fake-type\" is not allowed. Supported types are: \"collection, content\"."]}}}}', true), json_decode($client->getResponse()->getContent(), true));
    }

    public function testNestedRoutes()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'root',
                'type' => 'collection',
            ],
        ]);

        $rootContent = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'root-child1',
                'type' => 'collection',
                'parent' => $rootContent['id'],
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'child1-root-child1',
                'type' => 'collection',
                'parent' => $content['id'],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_routes', ['id' => $rootContent['id']]));

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        self::assertArraySubset(json_decode('{"content":null,"static_prefix":"\/root","variable_pattern":"\/{slug}","parent":null,"children":[{"content":null,"static_prefix":"\/root\/root-child1","variable_pattern":"\/{slug}","parent":null,"children":[{"content":null,"static_prefix":"\/root\/root-child1\/child1-root-child1","variable_pattern":"\/{slug}","parent":null,"children":[],"level":2,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"child1-root-child1","position":null}],"level":1,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"root-child1","position":null}],"level":0,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"root","position":null}', true), $content);
    }

    public function testAssigningNotExistingRoute()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'root',
                'type' => 'collection',
                'parent' => 99999,
            ],
        ]);

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"message":"Validation Failed","errors":{"children":{"parent":{"errors":["The selected route does not exist!"]}}}}', true), $content);
    }
}
