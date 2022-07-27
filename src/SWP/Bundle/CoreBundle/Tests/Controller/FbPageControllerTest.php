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
use Symfony\Component\Routing\RouterInterface;

class FbPageControllerTest extends WebTestCase
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

    public function testCreatePage()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_create_facebook_pages'), [
                'pageId' => '1234567890987654321',
                'name' => 'Test Page',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertContains('"page_id":"1234567890987654321"', $content);
        self::assertContains('"name":"Test Page"', $content);
        self::assertContains('"access_token":null', $content);
        self::assertContains('"application":null', $content);
    }

    public function testPageDuplication()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_create_facebook_pages'), [
                'pageId' => '1234567890987654321',
                'name' => 'Test Page',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_create_facebook_pages'), [
                'pageId' => '1234567890987654321',
                'name' => 'Test Page',
        ]);
        self::assertEquals(409, $client->getResponse()->getStatusCode());
    }

    public function listPages()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_create_facebook_pages'), [
                'pageId' => '1234567890987654321',
                'name' => 'Test Page',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_list_facebook_pages'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $content['_embedded']['_items']);

        $client->request('POST', $this->router->generate('swp_api_create_facebook_pages'), [
                'pageId' => '1234567890987654000',
                'name' => 'Test Page 2',
        ]);

        $client->request('GET', $this->router->generate('swp_api_list_facebook_pages'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(2, $content['_embedded']['_items']);
    }

    public function testDeletePages()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_create_facebook_pages'), [
                'pageId' => '1234567890987654321',
                'name' => 'Test Page',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_list_facebook_pages'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $content['_embedded']['_items']);

        $client->request('DELETE', $this->router->generate('swp_api_delete_facebook_pages', ['id' => 1]));
        self::assertEquals(204, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_list_facebook_pages'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(0, $content['_embedded']['_items']);
    }
}
