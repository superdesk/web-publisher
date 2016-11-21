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
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Routing\RouterInterface;

class ContentListControllerTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Client
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
        $this->router = $this->getContainer()->get('router');
        $this->client = static::createClient();
    }

    public function testCreateNewContentListApi()
    {
        $response = $this->createNewContentList([
            'name' => 'Example automatic list',
            'type' => 'automatic',
            'description' => 'New list',
            'limit' => 5,
            'cacheLifeTime' => 30,
            'expression' => 'article.getLocale() == "en"',
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        self::assertArraySubset(json_decode('{"id":1,"name":"Example automatic list","description":"New list","type":"automatic","cache_life_time":30,"limit":5,"items":[],"expression":"article.getLocale() == \"en\"","enabled":true,"_links":{"self":{"href":"\/api\/v1\/content\/lists\/1"}}}', true), $content);
    }

    public function testCreateAndGetSingleContentListApi()
    {
        $response = $this->createNewContentList([
            'name' => 'Example automatic list',
            'type' => 'automatic',
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->client->request('GET', $this->router->generate('swp_api_content_show_lists', ['id' => $content['id']]));

        self::assertArraySubset(json_decode('{"id":1,"name":"Example automatic list","description":null,"type":"automatic","cache_life_time":null,"limit":null,"items":[],"expression":null,"enabled":true,"_links":{"self":{"href":"\/api\/v1\/content\/lists\/1"}}}', true), $content);
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

        $this->client->request('GET', $this->router->generate('swp_api_content_list_lists'));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        $content = json_decode($this->client->getResponse()->getContent(), true);

        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v1\/content\/lists\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/content\/lists\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/content\/lists\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"name":"Example automatic list","description":null,"type":"automatic","cache_life_time":null,"limit":null,"items":[],"expression":null,"enabled":true,"_links":{"self":{"href":"\/api\/v1\/content\/lists\/1"}}},{"id":2,"name":"Manual list","description":null,"type":"manual","cache_life_time":null,"limit":null,"items":[],"expression":null,"enabled":true,"_links":{"self":{"href":"\/api\/v1\/content\/lists\/2"}}}]}}', true), $content);
    }

    public function testDeleteContentList()
    {
        $response = $this->createNewContentList([
            'name' => 'Example automatic list',
            'type' => 'automatic',
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->client->request('DELETE', $this->router->generate('swp_api_content_delete_lists', ['id' => $content['id']]));
        self::assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteNotExistingContentList()
    {
        $this->client->request('DELETE', $this->router->generate('swp_api_content_delete_lists', ['id' => 99]));
        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateContentListApi()
    {
        $response = $this->createNewContentList([
            'name' => 'Example automatic list',
            'type' => 'automatic',
            'description' => 'New list',
            'limit' => 5,
            'cacheLifeTime' => 30,
            'expression' => 'article.getLocale() == "en"',
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => $content['id']]), [
            'content_list' => [
                'name' => 'Example automatic list edited',
                'type' => 'automatic',
                'description' => 'New list edited',
                'limit' => 2,
                'cacheLifeTime' => 60,
                'expression' => 'article.getPriority() > 4',
            ],
        ]);

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);

        self::assertArraySubset(json_decode('{"id":1,"name":"Example automatic list edited","description":"New list edited","type":"automatic","cache_life_time":60,"limit":2,"items":[],"expression":"article.getPriority() > 4","enabled":true,"_links":{"self":{"href":"\/api\/v1\/content\/lists\/1"}}}', true), $content);
    }

    private function createNewContentList(array $params)
    {
        $this->client->request('POST', $this->router->generate('swp_api_content_create_lists'), [
            'content_list' => $params,
        ]);

        return $this->client->getResponse();
    }
}
