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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;

final class MultipleWebsitesPublish extends WebTestCase
{
    const TEST_ITEM_CONTENT = '{"language": "en", "source": "superdesk publisher", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>\n<!-- EMBED START Image {id: \"embedded4905430171\"} -->\n<figure><img src=\"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http\" alt=\"test image\" srcset=\"//localhost:5000/api/upload/1234567890987654321a/raw?_schema=http 800w, //localhost:5000/api/upload/1234567890987654321c/raw?_schema=http 1079w\" /><figcaption>test image</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded4905430171\"} -->", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated": "2016-09-23T09:11:28+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": [], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Sydney", "pubstatus": "usable", "associations": {"embedded4905430171": {"renditions": {"16-9": {"height": 720, "mimetype": "image/jpeg", "width": 1079, "media": "1234567890987654321a", "href": "http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http"}, "4-3": {"height": 533, "mimetype": "image/jpeg", "width": 800, "media": "1234567890987654321b", "href": "http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http"}, "original": {"height": 2667, "mimetype": "image/jpeg", "width": 4000, "media": "1234567890987654321c", "href": "http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http"}}, "urgency": 3, "body_text": "test image", "versioncreated": "2016-08-17T17:46:52+0000", "guid": "tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6", "byline": "Pawe\u0142 Miko\u0142ajczuk", "pubstatus": "usable", "language": "en", "version": "2", "description_text": "test image", "priority": 6, "type": "picture", "service": [{"name": "Australian General News", "code": "a"}], "usageterms": "indefinite-usage", "mimetype": "image/jpeg", "headline": "test image", "located": "Porto"}}}';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        self::bootKernel();

        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
        ], true);

        $this->router = $this->getContainer()->get('router');
        $elasticaResseter = $this->getContainer()->get('fos_elastica.resetter');
        $elasticaResseter->resetAllIndexes();
    }

    public function testPackagePublishAndUnpublishToFromManyWebsites()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');

        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321a',
                'media' => new UploadedFile(__DIR__.'/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321b',
                'media' => new UploadedFile(__DIR__.'/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321c',
                'media' => new UploadedFile(__DIR__.'/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client = static::createClient();
        // create route for tenant1
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'articles',
                'type' => RouteInterface::TYPE_COLLECTION,
                'content' => null,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $clientContent = json_decode($client->getResponse()->getContent(), true);

        // update content list filters for tenant2
        $client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 1]), [
                    'filters' => sprintf(
                        '{"route":[%d],"author":["ADmin"],"metadata":{"located":"Sydney"}}',
                        $clientContent['id']
                    ),
            ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 2]), [
                    'filters' => sprintf(
                        '{"route":[%d],"author":["fakeeeee"],"metadata":{"located":"Sydney"}}',
                        $clientContent['id']
                    ),
            ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_content_list_lists'));
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(2, $content['total']);

        // create route for tenant2
        $client2 = static::createClient([], [
            'HTTP_HOST' => 'client2.'.$client->getContainer()->getParameter('env(SWP_DOMAIN)'),
            'HTTP_Authorization' => base64_encode('client2_token'),
        ]);

        $client2->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'articles',
                'type' => RouteInterface::TYPE_COLLECTION,
                'content' => null,
        ]);

        self::assertEquals(201, $client2->getResponse()->getStatusCode());
        $client2Content = json_decode($client2->getResponse()->getContent(), true);

        $client2->request('GET', $this->router->generate('swp_api_content_list_lists'));
        $content = json_decode($client2->getResponse()->getContent(), true);
        self::assertEquals(1, $content['total']);

        // update content list filters for tenant2
        $client2->request('PATCH',
            $this->router->generate('swp_api_content_update_lists', ['id' => 4]), [
                    'filters' => sprintf(
                        '{"route":[%d],"author":["ADmin"],"metadata":{"located":"Sydney"}}',
                        $client2Content['id']
                    ),
            ]);

        self::assertEquals(200, $client2->getResponse()->getStatusCode());

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
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 3,
                            'isPublishedFbia' => false,
                            'published' => true,
                        ],
                        [
                            'tenant' => '678iop',
                            'route' => 4,
                            'isPublishedFbia' => false,
                            'published' => true,
                        ],
                    ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        // check if article was added to content list for tenant2
        $client2->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 4]));
        self::assertEquals(200, $client2->getResponse()->getStatusCode());

        $contentListItems = json_decode($client2->getResponse()->getContent(), true);
        self::assertEquals($contentListItems['total'], 1);
        self::assertEquals('Abstract html test', $contentListItems['_embedded']['_items'][0]['content']['title']);

        // one list from tenant1 should contain one item
        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $contentListItems = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals($contentListItems['total'], 1);

        $client->request('GET', $this->router->generate('swp_api_core_list_items', ['id' => 2]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $contentListItems = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals($contentListItems['total'], 0);

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

        self::assertArrayHasKey('is_publishable', $content);
        self::assertEquals($content['is_publishable'], true);
        self::assertNotNull($content['published_at']);
        self::assertEquals($content['status'], 'published');
        self::assertEquals($content['route']['id'], 3);

        // check article is published on second tenant
        $client2->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client2->getResponse()->getStatusCode());
        $content = json_decode($client2->getResponse()->getContent(), true);

        self::assertArrayHasKey('is_publishable', $content);
        self::assertEquals($content['is_publishable'], true);
        self::assertNotNull($content['published_at']);
        self::assertEquals($content['status'], 'published');
        self::assertEquals($content['route']['id'], 4);

        // re-push content to the whole organization
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
            $this->router->generate('swp_api_core_list_packages')
        );
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        // publish to tenants within same organization
        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 1]), [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 3,
                            'isPublishedFbia' => false,
                            'published' => true,
                        ],
                        [
                            'tenant' => '678iop',
                            'route' => 4,
                            'isPublishedFbia' => false,
                            'published' => true,
                        ],
                    ],
            ]
        );
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_unpublish_package', ['id' => 1]), [
                    'tenants' => ['123abc', '678iop'],
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

        self::assertEquals($content['is_publishable'], false);
        self::assertNotNull($content['published_at']);
        self::assertEquals($content['status'], 'unpublished');
        self::assertEquals($content['route']['id'], 3);

        // check article is published on second tenant
        $client2->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        self::assertEquals(200, $client2->getResponse()->getStatusCode());
        $content = json_decode($client2->getResponse()->getContent(), true);

        self::assertEquals($content['is_publishable'], false);
        self::assertNotNull($content['published_at']);
        self::assertEquals($content['status'], 'unpublished');
        self::assertEquals($content['route']['id'], 4);
    }

    public function testPublishingOnlyOnOtherTenantThanReceivedContent()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');

        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321a',
                'media' => new UploadedFile(__DIR__.'/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321b',
                'media' => new UploadedFile(__DIR__.'/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321c',
                'media' => new UploadedFile(__DIR__.'/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client2 = static::createClient([], [
            'HTTP_HOST' => 'client2.'.$client->getContainer()->getParameter('env(SWP_DOMAIN)'),
            'HTTP_Authorization' => base64_encode('client2_token'),
        ]);

        $client2->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'articles',
                'type' => RouteInterface::TYPE_COLLECTION,
                'content' => null,
        ]);
        self::assertEquals(201, $client2->getResponse()->getStatusCode());

        $client2->request('POST', $this->router->generate('swp_api_core_publishing_destination_create'), [
            'publish_destination' => [
                'tenant' => '678iop',
                'route' => 3,
                'isPublishedFbia' => false,
                'published' => true,
                'packageGuid' => 'urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0',
            ],
        ]);
        self::assertEquals(200, $client2->getResponse()->getStatusCode());

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
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT
        );
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        //TODO: check new article serialized content (one send to webhooks)
    }
}
