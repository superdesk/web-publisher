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
    public function setUp()
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
            "_links": {
                "item": {
                    "href": "/api/v1/content/lists/1/items/1"
                },
                "list": {
                    "href": "/api/v1/content/lists/1"
                }
            },
            "content": {
                "_links": {
                    "online": {
                        "href": "/article-1"
                    },
                    "self": {
                        "href": "/api/v1/content/articles/article-1"
                    }
                },
                "body": "art1",
                "featureMedia": null,
                "id": 1,
                "isPublishable": true,
                "keywords": [
                ],
                "lead": null,
                "media": [
                ],
                "metadata": {
                    "byline": "John Smith",
                    "located": "Berlin"
                },
                "publishedAt": null,
                "publishEndDate": null,
                "publishStartDate": null,
                "route": {
                    "_links": {
                        "self": {
                            "href": "/api/v1/content/routes/3"
                        }
                    },
                    "articlesTemplateName": null,
                    "cacheTimeInSeconds": 0,
                    "children": [
                    ],
                    "content": null,
                    "id": 3,
                    "level": 0,
                    "name": "news",
                    "parent": null,
                    "position": 2,
                    "root": 3,
                    "staticPrefix": null,
                    "templateName": null,
                    "type": "collection",
                    "variablePattern": "/{slug}"
                },
                "slug": "article-1",
                "status": "published",
                "templateName": null,
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

        self::assertArraySubset(json_decode('{"id":1,"content":{"id":1,"title":"article1","body":"art1","slug":"article-1","publishedAt":null,"status":"published","route":{"id":3,"content":null,"staticPrefix":null,"variablePattern":"\/{slug}","root":3,"parent":null,"children":[],"level":0,"templateName":null,"articlesTemplateName":null,"type":"collection","cacheTimeInSeconds":0,"name":"news","position":2,"_links":{"self":{"href":"\/api\/v1\/content\/routes\/3"}}},"templateName":null,"publishStartDate":null,"publishEndDate":null,"isPublishable":true,"metadata":{"byline":"John Smith","located":"Berlin"},"media":[],"featureMedia":null,"lead":null,"keywords":[],"_links":{"self":{"href":"\/api\/v1\/content\/articles\/article-1"},"online":{"href":"\/article-1"}}},"position":0,"sticky":true,"enabled":true,"_links":{"list":{"href":"\/api\/v1\/content\/lists\/1"},"item":{"href":"\/api\/v1\/content\/lists\/1\/items\/1"}}}', true), $content);
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
            'content_list_item' => [
                'sticky' => true,
            ],
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
            'content_list_item' => [
                'sticky' => false,
            ],
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
        $listUpdatedAt = $listData['updatedAt'];

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
            'content_list' => [
                'updated_at' => $listUpdatedAt,
                'items' => [
                    [
                        'content_id' => 3,
                        'action' => 'delete',
                    ],
                ],
            ],
        ]);
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updatedAt'];
        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(3, $listItems['_embedded']['_items']);
        self::assertEquals(4, $listItems['_embedded']['_items'][2]['content']['id']);

        $this->client->request('PATCH', $this->router->generate('swp_api_core_batch_update_lists_item', [
            'listId' => 1,
        ]), [
            'content_list' => [
                'updated_at' => $listUpdatedAt,
                'items' => [
                    [
                        'content_id' => 3,
                        'action' => 'add',
                        'position' => '-1',
                    ],
                ],
            ],
        ]);
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updatedAt'];
        $this->client->request('GET', $listData['_links']['items']['href']);
        $listItems = json_decode($this->client->getResponse()->getContent(), true);
        self::assertCount(4, $listItems['_embedded']['_items']);
        self::assertEquals(3, $listItems['_embedded']['_items'][3]['content']['id']);

        $this->client->request('PATCH', $this->router->generate('swp_api_core_batch_update_lists_item', [
            'listId' => 1,
        ]), [
            'content_list' => [
                'updated_at' => $listUpdatedAt,
                'items' => [
                    [
                        'content_id' => 3,
                        'action' => 'move',
                        'position' => 2,
                    ],
                ],
            ],
        ]);
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updatedAt'];
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
        $listUpdatedAt = $listData['updatedAt'];

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
                'content_list' => [
                    'updated_at' => $listUpdatedAt,
                    'items' => [
                        [
                            'content_id' => 3,
                            'action' => 'delete',
                        ],
                        [
                            'content_id' => 3,
                            'action' => 'add',
                            'position' => '-1',
                        ],
                        [
                            'content_id' => 3,
                            'action' => 'move',
                            'position' => 2,
                        ],
                    ],
                ],
            ]
        );
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updatedAt'];

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
                'content_list' => [
                    'updated_at' => $listUpdatedAt,
                    'items' => [
                        [
                            'content_id' => 3,
                            'action' => 'delete',
                        ],
                        [
                            'content_id' => 2,
                            'action' => 'delete',
                        ],
                        [
                            'content_id' => 1,
                            'action' => 'delete',
                        ],
                    ],
                ],
            ]
        );
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        $listData = json_decode($this->client->getResponse()->getContent(), true);
        $listUpdatedAt = $listData['updatedAt'];
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
                'content_list' => [
                    'updated_at' => $listUpdatedAt,
                    'items' => [
                        [
                            'content_id' => 3,
                            'action' => 'add',
                        ],
                        [
                            'content_id' => 2,
                            'action' => 'add',
                        ],
                        [
                            'content_id' => 1,
                            'action' => 'add',
                        ],
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
}
