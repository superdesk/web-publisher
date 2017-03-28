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

final class ContentPushTest extends WebTestCase
{
    const TEST_ITEM_UPDATE_ORIGIN = '{"body_html": "<p>this is test body</p><p>footer text</p>", "profile": "57d91f4ec3a5bed769c59846", "versioncreated": "2017-03-08T11:23:34+0000", "description_text": "test abstract", "byline": "Test Persona", "place": [], "version": "2", "pubstatus": "usable", "guid": "urn:newsml:localhost:2017-03-08T12:18:57.190465:2ff36225-af01-4f39-9392-39e901838d99", "language": "en", "urgency": 3, "slugline": "test item update", "headline": "test headline", "service": [{"code": "news", "name": "News"}], "priority": 6, "firstcreated": "2017-03-08T11:18:57+0000", "located": "Berlin", "type": "text", "description_html": "<p>test abstract</p>"}';

    const TEST_ITEM_UPDATE_UPDATE_1 = '{"body_html": "<p>this is test body&nbsp;updated</p><p>footer text &nbsp;updated</p>", "profile": "57d91f4ec3a5bed769c59846", "versioncreated": "2017-03-08T11:26:08+0000", "description_text": "test abstract\u00a0updated", "byline": "Test Persona", "place": [], "version": "3", "pubstatus": "usable", "guid": "urn:newsml:localhost:2017-03-08T12:25:35.466333:df630dd5-9f99-42be-8e01-645a338a9521", "language": "en", "urgency": 3, "slugline": "test item update", "type": "text", "headline": "test headline", "service": [{"code": "news", "name": "News"}], "priority": 6, "firstcreated": "2017-03-08T11:25:35+0000", "evolvedfrom": "urn:newsml:localhost:2017-03-08T12:18:57.190465:2ff36225-af01-4f39-9392-39e901838d99", "located": "Berlin", "description_html": "<p>test abstract&nbsp;updated</p>"}';

    // update of TEST_ITEM_UPDATE_UPDATE_1
    const TEST_ITEM_UPDATE_UPDATE_2 = '{"body_html": "<p>this is test body&nbsp;updated 2</p><p>footer text &nbsp;updated 2</p>", "profile": "57d91f4ec3a5bed769c59846", "versioncreated": "2017-03-08T11:29:51+0000", "description_text": "test abstract\u00a0updated 2", "byline": "Test Persona", "place": [], "version": "4", "pubstatus": "usable", "guid": "urn:newsml:localhost:2017-03-08T12:29:27.222376:5aef400e-ee5c-4110-b929-04bd26e4a757", "language": "en", "urgency": 3, "slugline": "test item update", "type": "text", "headline": "test headline updated 2", "service": [{"code": "news", "name": "News"}], "priority": 6, "firstcreated": "2017-03-08T11:29:27+0000", "evolvedfrom": "urn:newsml:localhost:2017-03-08T12:25:35.466333:df630dd5-9f99-42be-8e01-645a338a9521", "located": "Berlin", "description_html": "<p>test abstract&nbsp;updated 2</p>"}';

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
        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/content_list.yml',
        ], true);
    }

    public function testArticleUpdates()
    {
        // submit original item
        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_UPDATE_ORIGIN
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'test-item-update'])
        );

        self::assertEquals(404, $client->getResponse()->getStatusCode());

        $content = $this->getPushedOrganizationArticle();

        self::assertEquals(1, $content['id']);
        self::assertEquals('new', $content['status']);
        self::assertFalse($content['isPublishable']);
        self::assertEquals('test headline', $content['title']);
        self::assertEquals('urn:newsml:localhost:2017-03-08T12:18:57.190465:2ff36225-af01-4f39-9392-39e901838d99', $content['code']);

        $this->assetsThereIsOnlyOneArticle();

        // update origin item
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_UPDATE_UPDATE_1
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assetsThereIsOnlyOneArticle();

        $content = $this->getPushedOrganizationArticle();

        self::assertEquals(1, $content['id']);
        self::assertEquals('new', $content['status']);
        self::assertFalse($content['isPublishable']);
        self::assertEquals('test headline', $content['title']);
        self::assertEquals('urn:newsml:localhost:2017-03-08T12:25:35.466333:df630dd5-9f99-42be-8e01-645a338a9521', $content['code']);

        // an update of the first update (i.e. second update of origin item)
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_UPDATE_UPDATE_2
        );

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assetsThereIsOnlyOneArticle();

        $content = $this->getPushedOrganizationArticle();

        self::assertEquals(1, $content['id']);
        self::assertEquals('new', $content['status']);
        self::assertFalse($content['isPublishable']);
        self::assertEquals('test headline updated 2', $content['title']);
        self::assertEquals('urn:newsml:localhost:2017-03-08T12:29:27.222376:5aef400e-ee5c-4110-b929-04bd26e4a757', $content['code']);
        $this->assetsThereIsOnlyOneArticle();
    }

    private function getPushedOrganizationArticle()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            $this->router->generate('swp_api_core_show_organization_article', ['id' => 1])
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true);
    }

    private function assetsThereIsOnlyOneArticle()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            $this->router->generate('swp_api_core_list_organization_articles')
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(1, $content['total']);
    }
}
