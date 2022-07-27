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
    public function setUp(): void
    {
      parent::setUp();

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
            'filters' => '{"metadata":{"located":"Sydney"}}',
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        self::assertArraySubset(json_decode('{"id":1,"name":"Example automatic list","description":"New list","type":"automatic","cache_life_time":30,"limit":5,"filters":{"metadata":{"located":"Sydney"}},"enabled":true,"_links":{"self":{"href":"\/api\/v2\/content\/lists\/1"},"items":{"href":"\/api\/v2\/content\/lists\/1\/items\/"}}}', true), $content);
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

        self::assertArraySubset(json_decode('{"id":1,"name":"Example automatic list","description":null,"type":"automatic","cache_life_time":null,"limit":null,"filters":[],"enabled":true,"_links":{"self":{"href":"\/api\/v2\/content\/lists\/1"},"items":{"href":"\/api\/v2\/content\/lists\/1\/items\/"}}}', true), $content);
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

        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":2,"_links":{"self":{"href":"\/api\/v2\/content\/lists\/?page=1&limit=10"},"first":{"href":"\/api\/v2\/content\/lists\/?page=1&limit=10"},"last":{"href":"\/api\/v2\/content\/lists\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"name":"Example automatic list","description":null,"type":"automatic","cache_life_time":null,"limit":null,"filters":[],"enabled":true,"_links":{"self":{"href":"\/api\/v2\/content\/lists\/1"},"items":{"href":"\/api\/v2\/content\/lists\/1\/items\/"}}},{"id":2,"name":"Manual list","description":null,"type":"manual","cache_life_time":null,"limit":null,"filters":[],"enabled":true,"_links":{"self":{"href":"\/api\/v2\/content\/lists\/2"},"items":{"href":"\/api\/v2\/content\/lists\/2\/items\/"}}}]}}', true), $content);
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
            'filters' => '{"metadata":{"located":"Sydney"}}',
        ]);

        self::assertEquals(201, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => $content['id']]), [
                'name' => 'Example automatic list edited',
                'type' => 'automatic',
                'description' => 'New list edited',
                'limit' => 2,
                'cacheLifeTime' => 60,
                'filters' => '{"metadata":{"located":"Sydney"},"route":[1,2]}',
        ]);

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);

        self::assertArraySubset(json_decode('{"id":1,"name":"Example automatic list edited","description":"New list edited","type":"automatic","cache_life_time":60,"limit":2,"filters":{"metadata":{"located":"Sydney"},"route":[1,2]},"enabled":true,"_links":{"self":{"href":"\/api\/v2\/content\/lists\/1"},"items":{"href":"\/api\/v2\/content\/lists\/1\/items\/"}}}', true), $content);
    }

    public function testContentListItemsByRouteFiltersApi()
    {
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list_item.yml',
        ], true);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"metadata":{},"author":[],"route":[3,4]}',
            ]);

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"metadata":[],"author":[],"route":[3,4]}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(4, $content['total']);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"route":[3]}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"route":[3]}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(2, $content['total']);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"route":[4]}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"route":[4]}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(2, $content['total']);
    }

    public function testContentListItemsByPublishedAfterFiltersApi()
    {
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list_item.yml',
        ], true);

        $yesterday = (new \DateTime('yesterday'))->format('Y-m-d');
        $tomorrow = (new \DateTime('tomorrow'))->format('Y-m-d');

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                'filters' => '{"published_after":"'.$yesterday.'"}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();
        self::assertStringContainsString('"filters":{"published_after":"'.$yesterday.'"}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(5, $content['total']);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                'filters' => '{"published_after":"'.$tomorrow.'"}',
            ]
        );
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();
        self::assertStringContainsString('"filters":{"published_after":"'.$tomorrow.'"}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(1, $content['total']);
    }

    public function testEmbedingFirstContentListItemsInContentList()
    {
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list_item.yml',
        ], true);

        $this->client->request('GET', $this->router->generate('swp_api_content_list_lists', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(4, $content['_embedded']['_items'][0]['latest_items']);
        self::assertEquals('article1', $content['_embedded']['_items'][0]['latest_items'][0]['content']['title']);
    }

    public function testContentListItemsByAuthorFiltersApi()
    {
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list_item.yml',
        ], true);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"author":[{"id":999}]}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"author":[{"id":999}]}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(0, $content['total']);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"author":[{"id":2}]}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"author":[{"id":2}]}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(2, $content['total']);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"author":[{"id":2},{"id":1}]}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"author":[{"id":2},{"id":1}]}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(4, $content['total']);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"author":[{"id":3},{"id":1}]}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"author":[{"id":3},{"id":1}]}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(3, $content['total']);
    }

    public function testContentListItemsByRouteNamesApi()
    {
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list_item.yml',
        ], true);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"route":"politics"}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"route":"politics"}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(1, $content['total']);
    }

    public function testContentListItemsByRouteIdsAsStringApi()
    {
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list_item.yml',
        ], true);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"route":["5"]}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"route":["5"]}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(1, $content['total']);
    }

    public function testContentListItemsByManyFiltersApi()
    {
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list_item.yml',
        ], true);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"author":[{"id":2}],"route":[5]}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"author":[{"id":2}],"route":[5]}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(0, $content['total']);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"author":[{"id":2}],"route":[4]}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"author":[{"id":2}],"route":[4]}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(2, $content['total']);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"author":[{"id":2}],"route":[4],"metadata":{"located":"Warsaw"}}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"author":[{"id":2}],"route":[4],"metadata":{"located":"Warsaw"}}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(0, $content['total']);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"author":[{"id":1}],"route":[3],"metadata":{"located":"Berlin"}}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"author":[{"id":1}],"route":[3],"metadata":{"located":"Berlin"}}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(1, $content['total']);

        $this->client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"author":[{"id":3}],"route":[5],"metadata":{"located":"Warsaw"}}',
            ]
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();

        self::assertStringContainsString('"filters":{"author":[{"id":3}],"route":[5],"metadata":{"located":"Warsaw"}}', $content);
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(1, $content['total']);
    }

    public function testLinkingAndUnlinkingItemsToContentListApi()
    {
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
        ], true);
        $response = $this->createNewContentList([
            'name' => 'Manual list',
            'type' => 'manual',
        ]);
        $contentListId = json_decode($response->getContent(), true)['id'];

        $client = static::createClient();
        $client->request('LINK', $this->router->generate('swp_api_content_list_link_unlink', ['id' => $contentListId]), [], [], [
            'HTTP_LINK' => '</api/v2/content/articles/1; rel="article">',
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $client->request('GET', json_decode($client->getResponse()->getContent(), true)['_links']['items']['href']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        self::assertCount(1, json_decode($client->getResponse()->getContent(), true)['_embedded']['_items']);

        $client->request('UNLINK', $this->router->generate('swp_api_content_list_link_unlink', ['id' => $contentListId]), [], [], [
            'HTTP_LINK' => '</api/v2/content/articles/1; rel="article">',
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $client->request('GET', json_decode($client->getResponse()->getContent(), true)['_links']['items']['href']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        self::assertCount(0, json_decode($client->getResponse()->getContent(), true)['_embedded']['_items']);
    }

    public function testLinkingOnExactPositionApi()
    {
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
        ], true);
        $response = $this->createNewContentList([
            'name' => 'Manual list',
            'type' => 'manual',
        ]);
        $contentListId = json_decode($response->getContent(), true)['id'];

        $client = static::createClient();
        $client->request('LINK', $this->router->generate('swp_api_content_list_link_unlink', ['id' => $contentListId]), [], [], [
            'HTTP_LINK' => '</api/v2/content/articles/1; rel="widget">',
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', json_decode($client->getResponse()->getContent(), true)['_links']['items']['href']);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertCount(1, json_decode($client->getResponse()->getContent(), true)['_embedded']['_items']);

        // Move article 2 on position 1
        $client->request('LINK', $this->router->generate('swp_api_content_list_link_unlink', ['id' => $contentListId]), [], [], [
            'HTTP_LINK' => '</api/v2/content/articles/2; rel="widget">,<1; rel="position">',
        ]);
        $client->request('GET', json_decode($client->getResponse()->getContent(), true)['_links']['items']['href']);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $requestResult = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(2, $requestResult['_embedded']['_items']);
        self::assertEquals(1, $requestResult['_embedded']['_items'][1]['position']);
        self::assertEquals(2, $requestResult['_embedded']['_items'][1]['content']['id']);

        // Move article 2 on position 0
        $client->request('LINK', $this->router->generate('swp_api_content_list_link_unlink', ['id' => $contentListId]), [], [], [
            'HTTP_LINK' => '</api/v2/content/articles/2; rel="widget">,<0; rel="position">',
        ]);

        $client->request('GET', json_decode($client->getResponse()->getContent(), true)['_links']['items']['href']);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $requestResult = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(2, $requestResult['_embedded']['_items']);
        self::assertEquals(0, $requestResult['_embedded']['_items'][0]['position']);
        self::assertEquals(2, $requestResult['_embedded']['_items'][0]['content']['id']);
    }

    private function createNewContentList(array $params)
    {
        $this->client->request('POST', $this->router->generate('swp_api_content_create_lists'), $params);

        return $this->client->getResponse();
    }
}
