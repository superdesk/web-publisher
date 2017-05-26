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

use SWP\Bundle\FixturesBundle\WebTestCase;

class AuthControllerTest extends WebTestCase
{
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

    public function testFailureAuthentication()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_auth'), [
            'auth' => [
                'username' => 'some.fake.username',
                'password' => 'wrongPassword',
            ],
        ]);

        self::assertEquals(401, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('status', $content);
        self::assertArrayHasKey('message', $content);
        self::assertEquals($content['status'], 401);
        self::assertEquals($content['message'], 'Unauthorized');
    }

    public function testSuccessAuthentication()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_auth'), [
            'auth' => [
                'username' => 'test.user',
                'password' => 'testPassword',
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('token', $content);
        self::assertArrayHasKey('api_key', $content['token']);
        self::assertArrayHasKey('valid_to', $content['token']);
        self::assertArrayHasKey('user', $content);
        self::assertEquals($content['user']['username'], 'test.user');
    }

    public function testSuccessAuthenicationTwice()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_auth'), [
            'auth' => [
                'username' => 'test.user',
                'password' => 'testPassword',
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        $token = $content['token'];
        self::assertArrayHasKey('token', $content);

        $client->request('POST', $this->router->generate('swp_api_auth'), [
            'auth' => [
                'username' => 'test.user',
                'password' => 'testPassword',
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals($token, $content['token']);
        self::assertArrayHasKey('token', $content);
    }

    public function testAuthenticationToMultipleTenants()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_auth'), [
            'auth' => [
                'username' => 'test.user',
                'password' => 'testPassword',
            ],
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $domain = $client->getContainer()->getParameter('domain');

        $client2 = static::createClient([], [
            'HTTP_HOST' => 'client2.'.$domain,
            'HTTP_Authorization' => base64_encode('client2_token'),
        ]);
        $client2->request('GET', $this->router->generate('swp_api_user_get_user_profile', ['id' => 1]));
        self::assertEquals(200, $client2->getResponse()->getStatusCode());

        $client1 = static::createClient([], [
            'HTTP_HOST' => 'client1.'.$domain,
            'HTTP_Authorization' => base64_encode('client2_token'),
        ]);
        $client1->request('GET', $this->router->generate('swp_api_user_get_user_profile', ['id' => 1]));
        self::assertEquals(403, $client1->getResponse()->getStatusCode());

        $client = static::createClient([], [
            'HTTP_Authorization' => base64_encode('client2_token'),
        ]);
        $client->request('GET', $this->router->generate('swp_api_user_get_user_profile', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client = static::createClient([], [
            'HTTP_Authorization' => base64_encode('client1_token'),
        ]);
        $client->request('GET', $this->router->generate('swp_api_user_get_user_profile', ['id' => 1]));
        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
