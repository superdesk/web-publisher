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

class FbApplicationControllerTest extends WebTestCase
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

    public function testCreateApplication()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_create_facebook_applications'), [
                'appId' => '1234567890987654321',
                'appSecret' => 'ge56g3wegsysd56h6d76z47sugy56hts6gyd',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        self::assertStringContainsString('"app_id":"1234567890987654321"', $content);
        self::assertStringContainsString('"app_secret":"ge56g3wegsysd56h6d76z47sugy56hts6gyd"', $content);
    }

    public function testCreateDumplicateApplication()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_create_facebook_applications'), [
                'appId' => '1234567890987654321',
                'appSecret' => 'ge56g3wegsysd56h6d76z47sugy56hts6gyd',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_create_facebook_applications'), [
                'appId' => '1234567890987654321',
                'appSecret' => 'ge56g3wegsysd56h6d76z47sugy56hts6gyd',
        ]);
        self::assertEquals(409, $client->getResponse()->getStatusCode());
    }

    public function testListApplications()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_create_facebook_applications'), [
                'appId' => '1234567890987654321',
                'appSecret' => 'ge56g3wegsysd56h6d76z47sugy56hts6gyd',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_list_facebook_applications'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $content['_embedded']['_items']);
    }

    public function testDeleteApplications()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_create_facebook_applications'), [
                'appId' => '1234567890987654321',
                'appSecret' => 'ge56g3wegsysd56h6d76z47sugy56hts6gyd',
        ]);

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_list_facebook_applications'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(1, $content['_embedded']['_items']);

        $client->request('DELETE', $this->router->generate('swp_api_delete_facebook_applications', ['id' => 1]));
        self::assertEquals(204, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_list_facebook_applications'));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(0, $content['_embedded']['_items']);
    }
}
