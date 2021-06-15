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

class ContentListItemControllerTest extends WebTestCase
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
        self::bootKernel();

        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
        $this->router = $this->getContainer()->get('router');
        $this->client = static::createClient();

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/list_content.yml',
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list_item.yml',
        ], true);
    }

    public function testListingContentListsItemsApi()
    {
        $this->client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);

        self::assertCount(4, $content['_embedded']['_items']);
        self::assertEquals(0, $content['_embedded']['_items'][0]['position']);

        self::assertArraySubset(json_decode('
        {
            "content": {
                "_links": {
                    "online": {
                        "href": "/article-1"
                    },
                    "self": {
                        "href": "/api/v2/content/articles/article-1"
                    }
                },
                "route": {
                    "_links": {
                        "self": {
                            "href": "/api/v2/content/routes/3"
                        }
                    }
                },
                "status": "published",
                "title": "article1"
            },
            "enabled": true,
            "id": 1,
            "position": 0,
            "sticky": true
        }', true), $content['_embedded']['_items'][0]);

        self::assertEquals(2, $content['_embedded']['_items'][1]['position']);
        self::assertEquals(1, $content['_embedded']['_items'][2]['position']);
        self::assertEquals(3, $content['_embedded']['_items'][3]['position']);
    }

    public function testGetSingleContentListItem()
    {
        $this->client->request('GET', $this->router->generate('swp_api_core_show_lists_item', [
            'id' => 1,
            'listId' => 1,
        ]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArraySubset(json_decode('{"id":1,"content":{"id":1,"title":"article1","body":"art1","slug":"article-1","status":"published","route":{"id":3,"content":null,"static_prefix":null,"variable_pattern":"\/{slug}","parent":null,"children":[],"lft":3,"rgt": 4,"level":0,"template_name":null,"articles_template_name":null,"type":"collection","cache_time_in_seconds":0,"name":"news","position":1,"_links":{"self":{"href":"\/api\/v2\/content\/routes\/3"}}},"template_name":null,"publish_start_date":null,"publish_end_date":null,"is_publishable":true,"metadata":{"byline":"John Smith","located":"Berlin"},"media":[],"feature_media":null,"lead":null,"keywords":[],"_links":{"self":{"href":"\/api\/v2\/content\/articles\/article-1"},"online":{"href":"\/article-1"}}},"position":0,"sticky":true,"enabled":true,"_links":{"list":{"href":"\/api\/v2\/content\/lists\/1"},"item":{"href":"\/api\/v2\/content\/lists\/1\/items\/1"}}}', true), $content);
    }

    public function testGetSingleArticleAndItsContentLists()
    {
        $this->client->request('GET', $this->router->generate('swp_api_content_show_articles', [
            'id' => 1,
        ]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(1, $content['content_lists']);
        self::assertEquals(1, $content['content_lists'][0]['id']);
    }

    public function testGetSingleListItemWhenItemDoesntExist()
    {
        $this->client->request('GET', $this->router->generate('swp_api_core_show_lists_item', [
            'id' => 9999,
            'listId' => 1,
        ]));

        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testGetSingleListItemWhenListDoesntExist()
    {
        $this->client->request('GET', $this->router->generate('swp_api_core_show_lists_item', [
            'id' => 2,
            'listId' => 9999,
        ]));

        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testStickUnstickListItemContent()
    {
        $this->client->request('PATCH', $this->router->generate('swp_api_core_update_lists_item', [
            'id' => 2,
            'listId' => 1,
        ]), [
                'sticky' => true,
        ]);

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', $this->router->generate('swp_api_core_show_lists_item', [
            'id' => 2,
            'listId' => 1,
        ]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);

        self::assertArraySubset(json_decode('{"sticky":true}', true), $content);

        $this->client->request('PATCH', $this->router->generate('swp_api_core_update_lists_item', [
            'id' => 2,
            'listId' => 1,
        ]), [
                'sticky' => false,
        ]);

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', $this->router->generate('swp_api_core_show_lists_item', [
            'id' => 2,
            'listId' => 1,
        ]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $content = json_decode($this->client->getResponse()->getContent(), true);

        self::assertArraySubset(json_decode('{"sticky":false}', true), $content);
    }

    public function testBatchUpdate()
    {
        $this->client->request('GET', $this->router->generate('swp_api_content_show_lists', [
            'id' => 1,
        ]));
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updated_at'];

        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(4, $listItems['_embedded']['_items']);
        self::assertEquals(1, $listItems['_embedded']['_items'][0]['content']['id']);
        self::assertEquals(3, $listItems['_embedded']['_items'][1]['content']['id']);
        self::assertEquals(2, $listItems['_embedded']['_items'][2]['content']['id']);
        self::assertEquals(4, $listItems['_embedded']['_items'][3]['content']['id']);

        $this->client->request('PATCH', $this->router->generate('swp_api_core_batch_update_lists_item', [
            'listId' => 1,
        ]), [
                'updatedAt' => $listUpdatedAt,
                'items' => [
                    [
                        'contentId' => 3,
                        'action' => 'delete',
                    ],
                ],
        ]);
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updated_at'];
        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(3, $listItems['_embedded']['_items']);
        self::assertEquals(4, $listItems['_embedded']['_items'][2]['content']['id']);

        $this->client->request('PATCH', $this->router->generate('swp_api_core_batch_update_lists_item', [
            'listId' => 1,
        ]), [
                'updatedAt' => $listUpdatedAt,
                'items' => [
                    [
                        'contentId' => 3,
                        'action' => 'add',
                        'position' => '-1',
                    ],
                ],
        ]);
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updated_at'];
        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(4, $listItems['_embedded']['_items']);
        self::assertEquals(3, $listItems['_embedded']['_items'][3]['content']['id']);

        $this->client->request('PATCH', $this->router->generate('swp_api_core_batch_update_lists_item', [
            'listId' => 1,
        ]), [
                'updatedAt' => $listUpdatedAt,
                'items' => [
                    [
                        'contentId' => 3,
                        'action' => 'move',
                        'position' => 2,
                    ],
                ],
        ]);
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(4, $listItems['_embedded']['_items']);
        self::assertEquals(3, $listItems['_embedded']['_items'][2]['content']['id']);
        self::assertEquals(4, $listItems['_embedded']['_items'][3]['content']['id']);
    }

    public function testMultipleBatchUpdate()
    {
        $this->client->request(
            'GET',
            $this->router->generate(
                'swp_api_content_show_lists',
                [
                    'id' => 1,
                ]
            )
        );
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updated_at'];

        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(4, $listItems['_embedded']['_items']);
        self::assertEquals(1, $listItems['_embedded']['_items'][0]['content']['id']);
        self::assertEquals(3, $listItems['_embedded']['_items'][1]['content']['id']);
        self::assertEquals(2, $listItems['_embedded']['_items'][2]['content']['id']);
        self::assertEquals(4, $listItems['_embedded']['_items'][3]['content']['id']);

        $this->client->request(
            'PATCH',
            $this->router->generate(
                'swp_api_core_batch_update_lists_item',
                [
                    'listId' => 1,
                ]
            ),
            [
                    'updatedAt' => $listUpdatedAt,
                    'items' => [
                        [
                            'contentId' => 3,
                            'action' => 'delete',
                        ],
                        [
                            'contentId' => 3,
                            'action' => 'add',
                            'position' => '-1',
                        ],
                        [
                            'contentId' => 3,
                            'action' => 'move',
                            'position' => 2,
                        ],
                    ],
            ]
        );
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updated_at'];

        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertCount(4, $listItems['_embedded']['_items']);
        self::assertEquals(1, $listItems['_embedded']['_items'][0]['content']['id']);
        self::assertEquals(2, $listItems['_embedded']['_items'][1]['content']['id']);
        self::assertEquals(3, $listItems['_embedded']['_items'][2]['content']['id']);
        self::assertEquals(4, $listItems['_embedded']['_items'][3]['content']['id']);

        $this->client->request(
            'PATCH',
            $this->router->generate(
                'swp_api_core_batch_update_lists_item',
                [
                    'listId' => 1,
                ]
            ),
            [
                    'updatedAt' => $listUpdatedAt,
                    'items' => [
                        [
                            'contentId' => 3,
                            'action' => 'delete',
                        ],
                        [
                            'contentId' => 2,
                            'action' => 'delete',
                        ],
                        [
                            'contentId' => 1,
                            'action' => 'delete',
                        ],
                    ],
            ]
        );
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updated_at'];
        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(1, $listItems['_embedded']['_items']);

        $this->client->request(
            'PATCH',
            $this->router->generate(
                'swp_api_core_batch_update_lists_item',
                [
                    'listId' => 1,
                ]
            ),
            [
                    'updatedAt' => $listUpdatedAt,
                    'items' => [
                        [
                            'contentId' => 3,
                            'action' => 'add',
                        ],
                        [
                            'contentId' => 2,
                            'action' => 'add',
                        ],
                        [
                            'contentId' => 1,
                            'action' => 'add',
                        ],
                    ],
            ]
        );
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(4, $listItems['_embedded']['_items']);
        self::assertEquals(1, $listItems['_embedded']['_items'][0]['content']['id']);
        self::assertEquals(2, $listItems['_embedded']['_items'][1]['content']['id']);
        self::assertEquals(3, $listItems['_embedded']['_items'][2]['content']['id']);
        self::assertEquals(4, $listItems['_embedded']['_items'][3]['content']['id']);
    }

    public function testBatchUpdateOnLimitedList()
    {
        $this->client->request('POST', $this->router->generate('swp_api_content_create_lists'), [
            'name' => 'Manual list',
            'type' => 'manual',
            'limit' => 4,
        ]);

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updated_at'];

        $this->client->request(
            'PATCH',
            $this->router->generate(
                'swp_api_core_batch_update_lists_item',
                [
                    'listId' => 5,
                ]
            ),
            [
                'updatedAt' => $listUpdatedAt,
                'items' => [
                    [
                        'contentId' => 1,
                        'action' => 'add',
                        'position' => '0',
                    ],
                    [
                        'contentId' => 2,
                        'action' => 'add',
                        'position' => '0',
                    ],
                    [
                        'contentId' => 3,
                        'action' => 'add',
                        'position' => '0',
                    ],
                ],
            ]
        );
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updated_at'];

        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(3, $listItems['_embedded']['_items']);
        self::assertEquals(3, $listItems['_embedded']['_items'][0]['content']['id']);
        self::assertEquals(2, $listItems['_embedded']['_items'][1]['content']['id']);
        self::assertEquals(1, $listItems['_embedded']['_items'][2]['content']['id']);

        $this->client->request(
            'PATCH',
            $this->router->generate(
                'swp_api_core_batch_update_lists_item',
                [
                    'listId' => 5,
                ]
            ),
            [
                'updatedAt' => $listUpdatedAt,
                'items' => [
                    [
                        'contentId' => 4,
                        'action' => 'add',
                        'position' => '0',
                    ],
                    [
                        'contentId' => 5,
                        'action' => 'add',
                        'position' => '0',
                    ],
                ],
            ]
        );
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(4, $listItems['_embedded']['_items']);
    }
}
