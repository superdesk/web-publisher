<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests;

use SWP\Bundle\FixturesBundle\WebTestCase;

final class AddArticleToListOnPublishTest extends WebTestCase
{
    const TEST_ITEM_CONTENT = '{"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated": "2016-09-23T09:11:28+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": [], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Sydney", "pubstatus": "usable"}';

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
        $this->databaseTool->loadAliceFixture([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
        ], true, null, 'doctrine', 0);
        $this->router = $this->getContainer()->get('router');
    }

    public function testAddArticleToContentListOnPublish()
    {
        $now = new \DateTime();
        $now = $now->format('Y-m-d');

        $client = static::createClient();
        $client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => sprintf(
                        '{"route":[%d],"author":["ADmin"],"metadata":{"located":"Sydney"},"publishedAt":"%s"}',
                        3,
                        $now
                    ),
            ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $contentUpdated = json_decode($client->getResponse()->getContent(), true);
        sleep(1);
        $this->prepareArticle();

        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(1, $content['total']);
        self::assertStringContainsString('Abstract html test', $client->getResponse()->getContent());
        $client->request('GET', $this->router->generate('swp_api_content_show_lists', ['id' => 1]));
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertNotEquals($content['updated_at'], $contentUpdated['updated_at']);
    }

    public function testAddArticleToListOnPublishWhenRouteCriteriaNotMet()
    {
        $this->prepareArticle();

        $client = static::createClient();
        $client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => '{"route":[99,22]}',
            ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals($content['total'], 0);
    }

    public function testAddArticleToListOnPublishWhenPublishedAtCriteriaNotMet()
    {
        $this->prepareArticle();

        $now = new \DateTime('+1 day');
        $now = $now->format('Y-m-d');

        $client = static::createClient();
        $client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => sprintf(
                        '{"publishedAt":"%s"}',
                        $now
                    ),
            ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals($content['total'], 0);
    }

    public function testAddArticleToListOnPublishWhenWithPublishedAfter()
    {
        $this->prepareArticle();

        $now = new \DateTime('-1 day');
        $now = $now->format('Y-m-d');

        $client = static::createClient();
        $client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => "{\"publishedAfter\":\"$now\"}",
            ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals($content['total'], 1);
    }

    public function testAddArticleToListOnPublishWhenWithPublishedBefore()
    {
        $this->prepareArticle();

        $now = new \DateTime('-1 day');
        $now = $now->format('Y-m-d');

        $client = static::createClient();
        $client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => "{\"publishedBefore\":\"$now\"}",
            ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals($content['total'], 0);
    }

    public function testAddArticleToContentListOnPublishWhenNoCriteriaMet()
    {
        $this->prepareArticle();

        $now = new \DateTime('+1 day');
        $now = $now->format('Y-m-d');

        $client = static::createClient();
        $client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => sprintf(
                        '{"author":["fake"],"metadata":{"located":"Sydney"},"publishedAt":"%s"}',
                        $now
                    ),
            ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals($content['total'], 0);
    }

    private function prepareArticle()
    {
        $client = static::createClient();

        // create route
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $routeContent = json_decode($client->getResponse()->getContent(), true);

        // push content
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(404, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => $routeContent['id'],
                            'isPublishedFbia' => false,
                            'published' => true,
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
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->router);
    }
}
