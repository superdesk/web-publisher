<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class RouteControllerTest extends WebTestCase
{
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);

        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
        ], null, 'doctrine_phpcr');

        $this->router = $this->getContainer()->get('router');
    }

    public function testCreateEmptyContentRoutesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'parent' => '/',
                'content' => null,
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertArraySubset(json_decode('{"id":"\/swp\/123456\/123abc\/routes\/simple-test-route","content":null,"static_prefix":null,"variable_pattern":null,"name":"simple-test-route","children":[],"id_prefix":"\/swp\/123456\/123abc\/routes","template_name":null,"type":"content","_links":{"self":{"href":"\/api\/v1\/content\/routes\/\/simple-test-route"}}}', true), json_decode($client->getResponse()->getContent(), true));
    }

    public function testCreateContentRoutesApi()
    {
        $this->loadFixtures([
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadTenantsData',
            'SWP\Bundle\FixturesBundle\DataFixtures\PHPCR\LoadSeparateArticlesData',
        ], null, 'doctrine_phpcr');

        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'parent' => '/',
                'content' => 'test-content-article',
                'cacheTimeInSeconds' => 1,
            ],
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertArraySubset(json_decode('{"id":"\/swp\/123456\/123abc\/routes\/simple-test-route","content":{"id":"\/swp\/123456\/123abc\/content\/test-content-article","title":"Test content article"},"static_prefix":null,"variable_pattern":null,"name":"simple-test-route","children":[],"id_prefix":"\/swp\/123456\/123abc\/routes","template_name":null,"type":"content","_links":{"self":{"href":"\/api\/v1\/content\/routes\/\/simple-test-route"}}, "cache_time_in_seconds":1}', true), $content);
    }

    public function testCreateAndUpdateRoutesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'parent' => '/',
                'content' => null,
                'cacheTimeInSeconds' => 1,
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"id":"\/swp\/123456\/123abc\/routes\/simple-test-route","content":null,"static_prefix":null,"variable_pattern":null,"name":"simple-test-route","children":[],"id_prefix":"\/swp\/123456\/123abc\/routes","template_name":null,"type":"content","cache_time_in_seconds":1,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/\/simple-test-route"}}}', $client->getResponse()->getContent());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 'simple-test-route']), [
            'route' => [
                'name' => 'simple-test-route-new-name',
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"id":"\/swp\/123456\/123abc\/routes\/simple-test-route-new-name","content":null,"static_prefix":null,"variable_pattern":null,"name":"simple-test-route-new-name","children":[],"id_prefix":"\/swp\/123456\/123abc\/routes","template_name":null,"type":"content","cache_time_in_seconds":1,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/\/simple-test-route-new-name"}}}', $client->getResponse()->getContent());

        $client->request('GET', $this->router->generate('swp_api_content_list_routes'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"page":1,"limit":10,"pages":1,"total":1,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/content\/routes\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":"\/swp\/123456\/123abc\/routes\/simple-test-route-new-name","content":null,"static_prefix":null,"variable_pattern":null,"name":"simple-test-route-new-name","children":[],"id_prefix":"\/swp\/123456\/123abc\/routes","template_name":null,"type":"content","cache_time_in_seconds":1,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/\/simple-test-route-new-name"}}}]}}', $client->getResponse()->getContent());
    }

    public function testCreateAndUpdateAndDeleteRoutesApi()
    {
        $client = static::createClient();
        $client->enableProfiler();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'parent' => '/',
                'content' => null,
                'cacheTimeInSeconds' => 1,
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"id":"\/swp\/123456\/123abc\/routes\/simple-test-route","content":null,"static_prefix":null,"variable_pattern":null,"name":"simple-test-route","children":[],"id_prefix":"\/swp\/123456\/123abc\/routes","template_name":null,"type":"content","cache_time_in_seconds":1,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/\/simple-test-route"}}}', $client->getResponse()->getContent());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-child-test-route',
                'type' => 'content',
                'parent' => 'simple-test-route',
                'content' => null,
                'cacheTimeInSeconds' => 1,
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"id":"\/swp\/123456\/123abc\/routes\/simple-test-route\/simple-child-test-route","content":null,"static_prefix":null,"variable_pattern":null,"name":"simple-child-test-route","children":[],"id_prefix":"\/swp\/123456\/123abc\/routes","template_name":null,"type":"content","cache_time_in_seconds":1,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/\/simple-test-route\/simple-child-test-route"}}}', $client->getResponse()->getContent());

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => 'simple-test-route']));
        $this->assertEquals(409, $client->getResponse()->getStatusCode());

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => 'simple-test-route/simple-child-test-route']));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        $client->request('DELETE', $this->router->generate('swp_api_content_delete_routes', ['id' => 'simple-test-route']));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testWithCustomTemplatesRoutesApi()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'simple-test-route',
                'type' => 'content',
                'parent' => '/',
                'template_name' => 'test.html.twig',
                'cacheTimeInSeconds' => 1,
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"id":"\/swp\/123456\/123abc\/routes\/simple-test-route","content":null,"static_prefix":null,"variable_pattern":null,"name":"simple-test-route","children":[],"id_prefix":"\/swp\/123456\/123abc\/routes","template_name":"test.html.twig","type":"content","cache_time_in_seconds":1,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/\/simple-test-route"}}}', $client->getResponse()->getContent());
    }
}
