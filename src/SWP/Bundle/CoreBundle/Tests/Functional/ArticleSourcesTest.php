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

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

final class ArticleSourcesTest extends WebTestCase
{
    use ArraySubsetAsserts;
    const TEST_PACKAGE = '{"versioncreated":"2016-05-25T11:53:15+0000","firstcreated":"2016-05-25T10:23:15+0000","source":"FOX News","pubstatus":"usable","guid":"urn:newsml:localhost:2017-01-27T12:41:05.620675:cklo9212a-4699-4560-b73d-b61d7325e5cc","headline":"test package 5","byline":"John Doe","subject":[{"name":"archaeology","code":"01001000"}],"urgency":3,"priority":6,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"example headline","version":"6","associations":{"main":{"versioncreated":"2016-05-25T11:14:52+0000","pubstatus":"usable","body_html":"<p>sadf sadf sdaf sadg dg sadfasdg sa<\/p><p>df&nbsp;<\/p><p>asd<\/p><p>fsa&nbsp;<\/p><p>f&nbsp;<\/p><p>asd f<\/p>","headline":"sadf sadf sadf\u00a0","byline":"sadfsda fsdf sadf","subject":[{"name":"archaeology","code":"01001000"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 1","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:14:51.697607:9ba267e4-fed7-4d08-9af0-36bc933eb0f4"},"story-1":{"versioncreated":"2016-05-25T11:53:14+0000","source":"CNN","pubstatus":"usable","body_html":"<p>asd fsadf sadf sadf sda<\/p><p>fsad<\/p><p>f&nbsp;<\/p><p>sad<\/p><p>f sadf sadfsadf&nbsp;<\/p><p>lorem ipsum 3<\/p>","headline":"lorem ipsum content 3\u00a0","byline":"John Doe","subject":[{"name":"theft","code":"02001003"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 3","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:53:14.419018:b698d547-35a5-4f0f-9167-3dbecb1dae78"},"story-0":{"versioncreated":"2016-05-25T11:35:43+0000","pubstatus":"usable","body_html":"<p>lorem ispum body&nbsp;<\/p>","headline":"cinema film festival item","description_text": "test abstract","byline":"John Doe","subject":[{"name":"film festival","code":"01005001"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 2 ","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:35:43.450626:91228dd7-853e-41c6-8bea-32b75496c618"}},"type":"composite","language":"en"}';

    const TEST_PACKAGE_NEW = '{"versioncreated":"2016-05-25T11:53:15+0000","firstcreated":"2016-05-25T10:23:15+0000","source":"FOX News","pubstatus":"usable","guid":"urn:newsml:localhost:2017-01-27T12:41:05.620675:c7899212a-4699-4560-b73d-b61d7325e5cc","headline":"test package 5","byline":"John Doe","subject":[{"name":"archaeology","code":"01001000"}],"urgency":3,"priority":6,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"example headline 2","version":"6","associations":{"main":{"versioncreated":"2016-05-25T11:14:52+0000","pubstatus":"usable","body_html":"<p>sadf sadf sdaf sadg dg sadfasdg sa<\/p><p>df&nbsp;<\/p><p>asd<\/p><p>fsa&nbsp;<\/p><p>f&nbsp;<\/p><p>asd f<\/p>","headline":"sadf sadf sadf\u00a0","byline":"sadfsda fsdf sadf","subject":[{"name":"archaeology","code":"01001000"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 1","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:14:51.697607:9ba267e4-fed7-4d08-9af0-36bc933eb0f4"},"story-1":{"versioncreated":"2016-05-25T11:53:14+0000","source":"CNN","pubstatus":"usable","body_html":"<p>asd fsadf sadf sadf sda<\/p><p>fsad<\/p><p>f&nbsp;<\/p><p>sad<\/p><p>f sadf sadfsadf&nbsp;<\/p><p>lorem ipsum 3<\/p>","headline":"lorem ipsum content 3\u00a0","byline":"John Doe","subject":[{"name":"theft","code":"02001003"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 3","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:53:14.419018:b698d547-35a5-4f0f-9167-3dbecb1dae78"},"story-0":{"versioncreated":"2016-05-25T11:35:43+0000","pubstatus":"usable","body_html":"<p>lorem ispum body&nbsp;<\/p>","headline":"cinema film festival item","description_text": "test abstract","byline":"John Doe","subject":[{"name":"film festival","code":"01005001"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 2 ","priority":6,"type":"text","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:35:43.450626:91228dd7-853e-41c6-8bea-32b75496c618"}},"type":"composite","language":"en"}';

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

        $this->router = $this->getContainer()->get('router');
    }

    public function testArticleSources()
    {
        $this->createRoute();
        $client = static::createClient();

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_PACKAGE
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->enableProfiler();
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
                    ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'example-headline'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset(['id' => 1, 'name' => 'FOX News'], $content['sources'][0]['article_source']);

        $client->request(
            'GET',
            $this->router->generate('swp_api_core_article_sources')
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset([['id' => 1, 'name' => 'FOX News']], $content['_embedded']['_items']);
    }

    public function testArticleSourcesIfAreNotDuplicated()
    {
        $this->createRoute();

        $client = static::createClient();

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_PACKAGE
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

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
                    ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_PACKAGE_NEW
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_core_publish_package', ['id' => 2]), [
                    'destinations' => [
                        [
                            'tenant' => '123abc',
                            'route' => 3,
                            'isPublishedFbia' => false,
                            'published' => true,
                        ],
                    ],
            ]
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'example-headline-2-0123456789abc'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset(['id' => 1, 'name' => 'FOX News'], $content['sources'][0]['article_source']);

        $client->request(
            'GET',
            $this->router->generate('swp_api_core_article_sources')
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset([['id' => 1, 'name' => 'FOX News']], $content['_embedded']['_items']);
    }

    public function testArticleSourcesWithDifferentTenants()
    {
        $this->createRoute();

        $client = static::createClient();

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_PACKAGE
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        // create route for tenant2
        $client2 = static::createClient([], [
            'HTTP_HOST' => 'client2.'.$client->getContainer()->getParameter('env(SWP_DOMAIN)'),
            'HTTP_Authorization' => base64_encode('client2_token'),
        ]);

        $client2->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'business',
                'type' => RouteInterface::TYPE_COLLECTION,
                'content' => null,
        ]);

        self::assertEquals(201, $client2->getResponse()->getStatusCode());

        $client2->request(
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
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'example-headline'])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset(['id' => 1, 'name' => 'FOX News'], $content['sources'][0]['article_source']);

        $client2->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'example-headline'])
        );

        self::assertEquals(200, $client2->getResponse()->getStatusCode());

        $content = json_decode($client2->getResponse()->getContent(), true);

        self::assertArraySubset(['id' => 2, 'name' => 'FOX News'], $content['sources'][0]['article_source']);

        $client->request(
            'GET',
            $this->router->generate('swp_api_core_article_sources')
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset([['id' => 1, 'name' => 'FOX News']], $content['_embedded']['_items']);

        $client2->request(
            'GET',
            $this->router->generate('swp_api_core_article_sources')
        );

        self::assertEquals(200, $client2->getResponse()->getStatusCode());

        $content = json_decode($client2->getResponse()->getContent(), true);

        self::assertArraySubset([['id' => 2, 'name' => 'FOX News']], $content['_embedded']['_items']);
    }

    private function createRoute()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
                'name' => 'articles',
                'type' => RouteInterface::TYPE_COLLECTION,
                'content' => null,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
    }
}
