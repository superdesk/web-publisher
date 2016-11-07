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

use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class ArticleAutoPublishTest extends WebTestCase
{
    const TEST_ITEM_CONTENT = '{"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": [], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Sydney", "pubstatus": "usable"}';

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

    public function testArticleAutoPublishBasedOnRule()
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

        $content = $this->createRouteAndPushContent();

        self::assertArrayHasKey('is_publishable', $content);
        self::assertEquals($content['is_publishable'], true);
        self::assertNotNull($content['published_at']);
        self::assertEquals($content['status'], 'published');
    }

    public function testArticleShouldNotBeAutoPublishedIfDoesNotMatchRule()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'rule' => [
                'expression' => 'article.getMetadataByKey("located") matches "/fake/"',
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

        $content = $this->createRouteAndPushContent();

        self::assertArrayHasKey('is_publishable', $content);
        self::assertEquals($content['is_publishable'], false);
        self::assertNull($content['published_at']);
        self::assertEquals($content['status'], 'new');
    }

    public function testArticleShouldNotBeAutoPublishedBasedOnRule()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'rule' => [
                'expression' => 'article.getMetadataByKey("located") matches "/Sydney/"',
                'priority' => 1,
                'configuration' => [
                    [
                        'key' => 'published',
                        'value' => false,
                    ],
                ],
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $content = $this->createRouteAndPushContent();

        self::assertArrayHasKey('is_publishable', $content);
        self::assertEquals($content['is_publishable'], false);
        self::assertNull($content['published_at']);
        self::assertEquals($content['status'], 'new');
    }

    private function createRouteAndPushContent()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

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

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }
}
