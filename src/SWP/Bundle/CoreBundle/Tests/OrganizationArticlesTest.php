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

namespace src\SWP\Bundle\CoreBundle\Tests;

use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class OrganizationArticlesTest extends WebTestCase
{
    const CONTENT_ARTICLE_1 = '{"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated":"2016-05-25T10:23:15+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": ["keyword1","keyword2"], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Warsaw", "pubstatus": "usable"}';

    const CONTENT_ARTICLE_2 = '{"language": "en", "slugline": "abstract-html-test-2", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated":"2016-05-25T10:23:15+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": ["keyword1","keyword2"], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test 2", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Warsaw", "pubstatus": "usable"}';

    /**
     * @var RouterInterface
     */
    protected $router;

    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
        $this->router = $this->getContainer()->get('router');
    }

    public function testOrganizationArticles()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::CONTENT_ARTICLE_1
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_organization_articles', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":1,"_links":{"self":{"href":"\/api\/v1\/organizations\/1\/articles\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/organizations\/1\/articles\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/organizations\/1\/articles\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":1,"title":"Abstract html test","body":"<p>some html body<\/p> ","slug":"abstract-html-test","publishedAt":null,"status":"new","route":null,"templateName":null,"publishStartDate":null,"publishEndDate":null,"isPublishable":false,"metadata":{"subject":[{"name":"lawyer","code":"02002001"}],"urgency":3,"priority":6,"located":"Warsaw","place":[{"country":"Australia","world_region":"Oceania","state":"Australian Capital Territory","qcode":"ACT","name":"ACT","group":"Australia"}],"service":[{"name":"Australian General News","code":"a"}],"type":"text","byline":"ADmin","guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0","edNote":null,"genre":null,"language":"en"},"media":[],"featureMedia":null,"lead":"some abstract text","keywords":["keyword1","keyword2"],"code":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0","tenant":null,"_links":{"self":{"href":"\/api\/v1\/content\/articles\/abstract-html-test"},"online":[{"href":"\/api\/v1\/content\/articles\/?slug=abstract-html-test"},{"href":"\/api\/v1\/content\/articles\/"}]}}]}}', true), $content);

        $client->request('GET', $this->router->generate('swp_api_core_list_organization_articles', ['id' => 2]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(0, $content['total']);

        $client = static::createClient([], [
            'HTTP_HOST' => 'client1.'.$client->getContainer()->getParameter('domain'),
            'HTTP_Authorization' => base64_encode('client1_token'),
        ]);

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::CONTENT_ARTICLE_2
        );

        $client->request('GET', $this->router->generate('swp_api_core_list_organization_articles', ['id' => 2]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset(json_decode('{"page":1,"limit":10,"pages":1,"total":1,"_links":{"self":{"href":"\/api\/v1\/organizations\/2\/articles\/?page=1&limit=10"},"first":{"href":"\/api\/v1\/organizations\/2\/articles\/?page=1&limit=10"},"last":{"href":"\/api\/v1\/organizations\/2\/articles\/?page=1&limit=10"}},"_embedded":{"_items":[{"id":2,"title":"Abstract html test 2","body":"<p>some html body<\/p> ","slug":"abstract-html-test-2","publishedAt":null,"status":"new","route":null,"templateName":null,"publishStartDate":null,"publishEndDate":null,"isPublishable":false,"metadata":{"subject":[{"name":"lawyer","code":"02002001"}],"urgency":3,"priority":6,"located":"Warsaw","place":[{"country":"Australia","world_region":"Oceania","state":"Australian Capital Territory","qcode":"ACT","name":"ACT","group":"Australia"}],"service":[{"name":"Australian General News","code":"a"}],"type":"text","byline":"ADmin","guid":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0","edNote":null,"genre":null,"language":"en"},"media":[],"featureMedia":null,"lead":"some abstract text","keywords":["keyword1","keyword2"],"code":"urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0","tenant":null,"_links":{"self":{"href":"\/api\/v1\/content\/articles\/abstract-html-test-2"},"online":[{"href":"\/api\/v1\/content\/articles\/?slug=abstract-html-test-2"},{"href":"\/api\/v1\/content\/articles\/"}]}}]}}', true), $content);
    }

    public function testOrganizationArticlesWhenAutoPublished()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_create_rule'), [
            'rule' => [
                'expression' => 'article.getMetadataByKey("language") matches "/en/"',
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

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::CONTENT_ARTICLE_1
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_organization_articles', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(1, $content['total']);
        self::assertEquals('published', $content['_embedded']['_items'][0]['status']);
        self::assertEquals(json_decode('{"id":1,"subdomain":null,"domainName":"localhost","name":"Default tenant","ampEnabled":true,"_links":{"self":{"href":"\/api\/v1\/tenants\/123abc"}}}', true), $content['_embedded']['_items'][0]['tenant']);
    }

    public function testOrganizationArticlesManualPublish()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::CONTENT_ARTICLE_1
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_organization_articles', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(1, $content['total']);
        self::assertEquals('new', $content['_embedded']['_items'][0]['status']);

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'abstract-html-test']), [
            'article' => [
                'status' => ArticleInterface::STATUS_PUBLISHED,
            ],
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_core_list_organization_articles', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(1, $content['total']);
        self::assertEquals('published', $content['_embedded']['_items'][0]['status']);
        self::assertEquals(json_decode('{"id":1,"subdomain":null,"domainName":"localhost","name":"Default tenant","ampEnabled":true,"_links":{"self":{"href":"\/api\/v1\/tenants\/123abc"}}}', true), $content['_embedded']['_items'][0]['tenant']);
    }
}
