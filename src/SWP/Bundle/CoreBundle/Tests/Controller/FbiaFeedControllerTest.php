<?php

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

namespace SWP\Bundle\CoreBundle\Tests\Controller;

use SWP\Bundle\FixturesBundle\WebTestCase;

class FbiaFeedControllerTest extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    private $router;

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

    public function testCreateFeed()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_content_create_lists'), [
                'name' => 'Example bucket',
                'type' => 'bucket',
                'description' => 'New FBIA list',
                'limit' => 0,
                'cacheLifeTime' => 0,
                'filters' => '{"metadata":{"locale":"en"}}',
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_create_facebook_pages'), [
                'pageId' => '1234567890987654321',
                'name' => 'Test Page',
        ]);
        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_create_facebook_instant_articles_feed'), [
                'contentBucket' => 1,
                'facebookPage' => 1,
                'mode' => 0,
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertContains('"mode":0', $content);

        $client->request('GET', $this->router->generate('swp_api_list_facebook_instant_articles_feed'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $content['_embedded']['_items']);
    }
}
