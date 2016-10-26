<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class ContentListControllerTest extends WebTestCase
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

    public function testCreateNewContentListApi()
    {
        $response = $this->createNewContentList([
            'name' => 'Example automatic list',
            'type' => 'automatic',
            'description' => 'New list',
            'limit' => 5,
            'cacheLifeTime' => 30,
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        self::assertArraySubset(json_decode('{"id":1,"name":"Example automatic list","description":"New list","type":"automatic","cache_life_time":30,"limit":5,"enabled":true,"_links":{"self":{"href":"\/api\/v1\/content\/lists\/1"}}}', true), $content);
    }

    public function testCreateAndGetSingleContentListApi()
    {
        $response = $this->createNewContentList([
            'name' => 'Example automatic list',
            'type' => 'automatic',
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_show_lists', ['id' => $content['id']]));

        self::assertArraySubset(json_decode('{"id":1,"name":"Example automatic list","description":null,"type":"automatic","cache_life_time":null,"limit":null,"enabled":true,"_links":{"self":{"href":"\/api\/v1\/content\/lists\/1"}}}', true), $content);
    }

    public function testCreateSingleContentListApiWithWrongType()
    {
        $response = $this->createNewContentList([
            'name' => 'Example automatic list',
            'type' => 'fake',
        ]);

        self::assertEquals(400, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        self::assertArraySubset(json_decode('{"message":"Validation Failed","errors":{"children":{"type":{"errors":["This value is not valid."]}}}}', true), $content);
    }

    public function testListingContentListsApi()
    {
        $response = $this->createNewContentList([
            'name' => 'Example automatic list',
            'type' => 'automatic',
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $response = $this->createNewContentList([
            'name' => 'Manual list',
            'type' => 'manual',
        ]);

        self::assertEquals(201, $response->getStatusCode());

        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_content_list_lists'));

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v1\/content\/lists\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/content\/lists\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/content\/lists\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"name":"Example automatic list","description":null,"type":"automatic","cache_life_time":null,"limit":null,"enabled":true,"_links":{"self":{"href":"\/api\/v1\/content\/lists\/1"}}},{"id":2,"name":"Manual list","description":null,"type":"manual","cache_life_time":null,"limit":null,"enabled":true,"_links":{"self":{"href":"\/api\/v1\/content\/lists\/2"}}}]}}', true), $content);
    }

    private function createNewContentList(array $params)
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_lists'), [
            'content_list' => $params,
        ]);

        return $client->getResponse();
    }
}
