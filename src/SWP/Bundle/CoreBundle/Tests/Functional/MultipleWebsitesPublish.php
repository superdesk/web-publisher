<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Functional;

use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

final class MultipleWebsitesPublish extends WebTestCase
{
    const TEST_ITEM_CONTENT = '{"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated": "2016-09-23T09:11:28+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": [], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Sydney", "pubstatus": "usable"}';

    /**
     * @var RouterInterface
     */
    private $router;

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

    public function testPackagePublishAndUnpublishToFromManyWebsites()
    {
        $client = static::createClient();
        // create route for tenant1
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => RouteInterface::TYPE_COLLECTION,
                'content' => null,
            ],
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        // create route for tenant2
        $client2 = static::createClient([], [
            'HTTP_HOST' => 'client2.'.$client->getContainer()->getParameter('domain'),
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

        // push content to the whole organization
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        // check package status
        $client->request(
            'GET',
            $this->router->generate('swp_api_core_show_package', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('new', $content['status']);

        // check if article exists in first tenant
        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );

        self::assertEquals(404, $client->getResponse()->getStatusCode());

        // check if article exists in second tenant
        $client2->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 1])
        );

        self::assertEquals(404, $client2->getResponse()->getStatusCode());

        // publish to tenants within same organization
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                'publish' => [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 3,
                        ],
                        [
                            'tenant' => '678iop',
                            'route' => 4,
                        ],
                    ],
                ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        // check package status
        $client->request(
            'GET',
            $this->router->generate('swp_api_core_show_package', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('published', $content['status']);

        // check article is published on first tenant
        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArrayHasKey('isPublishable', $content);
        self::assertEquals($content['isPublishable'], true);
        self::assertNotNull($content['publishedAt']);
        self::assertEquals($content['status'], 'published');
        self::assertEquals($content['route']['id'], 3);

        // check article is published on second tenant
        $client2->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client2->getResponse()->getStatusCode());
        $content = json_decode($client2->getResponse()->getContent(), true);

        self::assertArrayHasKey('isPublishable', $content);
        self::assertEquals($content['isPublishable'], true);
        self::assertNotNull($content['publishedAt']);
        self::assertEquals($content['status'], 'published');
        self::assertEquals($content['route']['id'], 4);

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_unpublish_package', ['id' => 1]), [
                'unpublish' => [
                    'tenants' => ['123abc', '678iop'],
                ],
            ]
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        // check package status
        $client->request(
            'GET',
            $this->router->generate('swp_api_core_show_package', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('unpublished', $content['status']);

        // check article is published on first tenant
        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals($content['isPublishable'], false);
        self::assertNotNull($content['publishedAt']);
        self::assertEquals($content['status'], 'unpublished');
        self::assertEquals($content['route']['id'], 3);

        // check article is published on second tenant
        $client2->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client2->getResponse()->getStatusCode());
        $content = json_decode($client2->getResponse()->getContent(), true);

        self::assertEquals($content['isPublishable'], false);
        self::assertNotNull($content['publishedAt']);
        self::assertEquals($content['status'], 'unpublished');
        self::assertEquals($content['route']['id'], 4);
    }
}
