<?php

declare(strict_types=1);

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

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

final class ArticleAutoPublishTest extends WebTestCase
{
    const TEST_ITEM_CONTENT = '{"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated": "2016-09-23T09:11:28+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": [], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Sydney", "pubstatus": "usable"}';

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

    public function testArticlePushToTenantBasedOnOrganizationRule()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_organization_rule'), [
            'rule' => [
                'expression' => 'package.getLocated() matches "/Sydney/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'destinations',
                        'value' => [
                            [
                                'tenant' => '123abc',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $this->createRouteAndPushContent();

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArrayHasKey('isPublishable', $content);
        self::assertEquals($content['isPublishable'], false);
        self::assertEquals($content['isPublishedFBIA'], false);
        self::assertNull($content['publishedAt']);
        self::assertNull($content['route']);
        self::assertEquals($content['status'], 'new');
    }

    public function testArticleRePushBasedOnOrganizationRule()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_organization_rule'), [
            'rule' => [
                'expression' => 'package.getLocated() matches "/Sydney/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'destinations',
                        'value' => [
                            [
                                'tenant' => '123abc',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $response = $this->pushContent();

        self::assertEquals(201, $response->getStatusCode());

        $response = $this->pushContent();

        self::assertEquals(201, $response->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArrayHasKey('isPublishable', $content);
        self::assertEquals($content['isPublishable'], false);
        self::assertEquals($content['isPublishedFBIA'], false);
        self::assertNull($content['publishedAt']);
        self::assertNull($content['route']);
        self::assertEquals($content['status'], 'new');
    }

    public function testArticleAutoPublishBasedOnTenantRule()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_organization_rule'), [
            'rule' => [
                'expression' => 'package.getLocated() matches "/Sydney/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'destinations',
                        'value' => [
                            [
                                'tenant' => '123abc',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'article',
                'type' => RouteInterface::TYPE_CONTENT,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $route = json_decode($client->getResponse()->getContent(), true);

        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'rule' => [
                'expression' => 'article.getMetadataByKey("located") matches "/Sydney/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'published',
                        'value' => true,
                    ],
                    [
                        'key' => 'route',
                        'value' => $route['id'],
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $response = $this->pushContent();

        self::assertEquals(201, $response->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArrayHasKey('isPublishable', $content);
        self::assertEquals($content['isPublishable'], true);
        self::assertEquals($content['isPublishedFBIA'], false);
        self::assertNotNull($content['publishedAt']);
        self::assertEquals($content['route']['id'], $route['id']);
        self::assertEquals($content['status'], 'published');
    }

    public function testArticleShouldNotBeAutoPublishedIfDoesNotMatchRule()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_organization_rule'), [
            'rule' => [
                'expression' => 'package.getLocated() matches "/fake/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'destinations',
                        'value' => [
                            [
                                'tenant' => '123abc',
                                'route' => 3,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $this->createRouteAndPushContent();

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );

        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testArticleShouldNotBeAutoPublishedBasedOnRule()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_organization_rule'), [
            'rule' => [
                'expression' => 'package.getLocated() matches "/Sydney/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'destinations',
                        'value' => [
                            [],
                        ],
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $this->createRouteAndPushContent();

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );

        self::assertEquals(404, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_core_show_package', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertCount(0, $content['articles']);
        self::assertEquals($content['status'], 'new');
    }

    public function testAssignRouteContentWhenArticleIsPublished()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'article',
                'type' => RouteInterface::TYPE_CONTENT,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $route = json_decode($client->getResponse()->getContent(), true);

        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'rule' => [
                'expression' => 'article.getMetadataByKey("located") matches "/Sydney/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'published',
                        'value' => true,
                    ],
                    [
                        'key' => 'route',
                        'value' => $route['id'],
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $this->createRouteAndPushContent();

        // create route for tenant2
        $client2 = static::createClient([], [
            'HTTP_HOST' => 'client2.localhost',
            'HTTP_Authorization' => base64_encode('client2_token'),
        ]);

        $client2->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => RouteInterface::TYPE_COLLECTION,
                'content' => null,
            ],
        ]);

        self::assertEquals(201, $client2->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'publish' => [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 4,
                            'fbia' => false,
                            'published' => true,
                        ],
                        [
                            'tenant' => '678iop',
                            'route' => 5,
                            'fbia' => false,
                            'published' => true,
                        ],
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_show_routes', [
            'id' => $route['id'],
        ]));

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $article = json_decode($client->getResponse()->getContent(), true);

        self::assertArrayHasKey('content', $content);
        self::assertEquals($content['content']['id'], $article['id']);
        self::assertEquals($article['route']['id'], $content['id']);

        $client2->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client2->getResponse()->getStatusCode());

        $article2 = json_decode($client2->getResponse()->getContent(), true);

        self::assertEquals($article2['route']['id'], 5);
        self::assertEquals($article2['status'], 'published');
    }

    public function testArticlePublishUnpublishBasedOnOrganizationAndArticleRules()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'rule' => [
                'expression' => 'article.getMetadataByKey("located") matches "/Sydney/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'published',
                        'value' => true,
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $this->createRouteAndPushContent();

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'publish' => [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 3,
                            'fbia' => false,
                            'published' => true,
                        ],
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $article = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('published', $article['status']);
    }

    public function testArticleAddToBucket()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_lists'), [
            'content_list' => [
                'name' => 'Example bucket',
                'type' => 'bucket',
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->createRouteAndPushContent();

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'publish' => [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 3,
                            'fbia' => true,
                            'published' => true,
                        ],
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => $content['id']]));

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(1, $content['total']);
    }

    public function testAlreadyAddedArticleRemovalFromBucket()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_lists'), [
            'content_list' => [
                'name' => 'Example bucket',
                'type' => 'bucket',
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $bucket = json_decode($client->getResponse()->getContent(), true);

        $this->createRouteAndPushContent();

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'publish' => [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 3,
                            'fbia' => true,
                            'published' => true,
                        ],
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => $bucket['id']]));

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertTrue($content['_embedded']['_items'][0]['content']['isPublishedFBIA']);
        self::assertEquals(1, $content['total']);

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'publish' => [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 3,
                            'fbia' => false,
                            'published' => true,
                        ],
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => $bucket['id']]));

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(0, $content['total']);
    }

    public function testDontAddArticleToBucket()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_lists'), [
            'content_list' => [
                'name' => 'Example bucket',
                'type' => 'bucket',
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->createRouteAndPushContent();

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'publish' => [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 3,
                            'fbia' => false,
                            'published' => true,
                        ],
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => $content['id']]));

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(0, $content['total']);
    }

    private function createRouteAndPushContent()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => RouteInterface::TYPE_COLLECTION,
                'content' => null,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $response = $this->pushContent();

        self::assertEquals(201, $response->getStatusCode());
    }

    private function pushContent()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT
        );

        return $client->getResponse();
    }
}
