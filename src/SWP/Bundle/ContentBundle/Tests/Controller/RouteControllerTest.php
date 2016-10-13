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
        self::assertEquals(json_decode('{"id":1,"content":null,"static_prefix":null,"variable_pattern":null,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":0,"name":"simple-test-route","position":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}}}', true), json_decode($client->getResponse()->getContent(), true));
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
        self::assertArraySubset(json_decode('{"id":2,"content":{"id":2,"title":"Test content article","body":"Test article content","slug":"test-content-article","status":"published","route":{"id":1,"content":null,"static_prefix":null,"variable_pattern":"\/{slug}","template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"news","position":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}}},"template_name":null,"updated_at":null,"publish_start_date":null,"publish_end_date":null,"is_publishable":true,"metadata":null,"media":null,"lead":null,"_links":{"self":{"href":"\/api\/v1\/content\/articles\/test-content-article"},"online":{"href":"\/test-content-article"}}},"static_prefix":null,"variable_pattern":null,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"simple-test-route","position":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/2"}}}', true), $content);
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
        self::assertEquals('{"id":1,"content":null,"static_prefix":null,"variable_pattern":null,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"simple-test-route","position":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}}}', $client->getResponse()->getContent());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 1]), [
            'route' => [
                'name' => 'simple-test-route-new-name',
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(
            ['id' => 1, 'name' => 'simple-test-route-new-name'],
            json_decode($client->getResponse()->getContent(), true)
        );

        $client->request('GET', $this->router->generate('swp_api_content_list_routes'));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals('{"page":1,"limit":10,"pages":1,"total":1,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"content":null,"static_prefix":null,"variable_pattern":null,"template_name":null,"articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"simple-test-route-new-name","position":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}}}]}}', $client->getResponse()->getContent());
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

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 1]), [
            'route' => [
                'name' => 'simple-edited-test-route',
                'type' => 'collection',
                'content' => 2,
                'cacheTimeInSeconds' => 50,
            ],
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":1,"content":{"id":2,"title":"Test content article","body":"Test article content","slug":"test-content-article","status":"published","route":null,"template_name":null,"updated_at":null,"publish_start_date":null,"publish_end_date":null,"is_publishable":true,"metadata":null,"media":null,"lead":null,"_links":{"self":{"href":"\/api\/v1\/content\/articles\/test-content-article"},"online":{"href":"\/test-content-article"}}},"static_prefix":null,"variable_pattern":"\/{slug}","template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":50,"name":"simple-edited-test-route","position":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}}}', true), json_decode($client->getResponse()->getContent(), true));

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => 1]));
        self::assertEquals(409, $client->getResponse()->getStatusCode());

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => 1]));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testWithCustomTemplatesRoutesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'template_name' => 'test.html.twig',
                'cacheTimeInSeconds' => 1,
            ],
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        self::assertArraySubset(json_decode('{"id":1,"content":null,"static_prefix":null,"variable_pattern":null,"template_name":"test.html.twig","articles_template_name":null,"type":"content","cache_time_in_seconds":1,"name":"simple-test-route","position":null,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/1"}}}', true), json_decode($client->getResponse()->getContent(), true));
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
        self::assertEquals($client->getResponse()->getContent(), '{"code":400,"message":"Validation Failed","errors":{"errors":["This form should not contain extra fields."],"children":{"name":{},"type":{},"template_name":{},"articles_template_name":{},"content":{},"cacheTimeInSeconds":{}}}}');
    }
}
