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
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use GuzzleHttp;

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
        $domain = $client->getContainer()->getParameter('env(SWP_DOMAIN)');

        $client2 = static::createClient([], [
            'HTTP_HOST' => 'client2.'.$domain,
            'HTTP_Authorization' => base64_encode('client2_token'),
        ]);
        $client2->request('GET', $this->router->generate('swp_api_user_get_user_profile', ['id' => 3]));
        self::assertEquals(200, $client2->getResponse()->getStatusCode());

        $client1 = static::createClient([], [
            'HTTP_HOST' => 'client1.'.$domain,
            'HTTP_Authorization' => base64_encode('client2_token'),
        ]);
        $client1->request('GET', $this->router->generate('swp_api_user_get_user_profile', ['id' => 3]));
        self::assertEquals(403, $client1->getResponse()->getStatusCode());

        $client = static::createClient([], [
            'HTTP_Authorization' => base64_encode('client2_token'),
        ]);
        $client->request('GET', $this->router->generate('swp_api_user_get_user_profile', ['id' => 3]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client = static::createClient([], [
            'HTTP_Authorization' => base64_encode('client1_token'),
        ]);
        $client->request('GET', $this->router->generate('swp_api_user_get_user_profile', ['id' => 2]));
        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testRegisterLoginAndUpdateProfileTest()
    {
        $client = static::createClient();
        $client->enableProfiler();
        $client->request('POST', $this->router->generate('swp_api_core_register_user'), [
            'user_registration' => [
                'email' => 'contact@example.com',
                'username' => 'sofab.contact',
                'plainPassword' => [
                    'first' => 'testPass',
                    'second' => 'testPass',
                ],
            ],
        ]);

        $this->activateUser($client);

        $client->request('POST', $this->router->generate('swp_api_auth'), [
            'auth' => [
                'username' => 'sofab.contact',
                'password' => 'testPass',
            ],
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $token = $data['token']['api_key'];

        $client = static::createClient([], [
            'HTTP_Authorization' => $token,
        ]);
        $client->request('GET', $this->router->generate('swp_api_user_get_user_profile', ['id' => 4]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('sofab.contact', $content['username']);

        $client->request('PATCH', $this->router->generate('swp_api_user_edit_user_profile', ['id' => 4]), [
            'user_profile' => [
                'email' => 'contact2@example.com',
                'username' => 'sofab.contact2',
                'firstName' => 'Test',
                'lastName' => 'User',
                'about' => 'About content',
            ],
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('sofab.contact2', $content['username']);
        self::assertEquals('Test', $content['firstName']);
        self::assertEquals('User', $content['lastName']);
        self::assertEquals('About content', $content['about']);
    }

    private function activateUser(Client $client): void
    {
        /** @var MessageDataCollector $swiftMailer */
        $swiftMailer = $client->getProfile()->getCollector('swiftmailer');
        /** @var \Swift_Message $message */
        $messageBody = $swiftMailer->getMessages()[0]->getBody();
        $client->followRedirect();
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        // activate URL
        preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $messageBody, $match);
        $client->request('GET', $match[0][0]);
        self::assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testSuperdeskAuthentication()
    {
        try {
            $baseUrl = ((array) $this->getContainer()->getParameter('superdesk_servers'))[0];
            $client = new GuzzleHttp\Client();
            $apiRequest = new GuzzleHttp\Psr7\Request('GET', $baseUrl.'/api/sessions');
            $client->send($apiRequest);
        } catch (GuzzleHttp\Exception\ConnectException $e) {
            $this->markTestSkipped('Superdesk fake server is offline');
        }

        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_auth_superdesk'), [
            'auth_superdesk' => [
                'session_id' => '4f5gwe4f5w45as4fd',
                'token' => 'test_token',
            ],
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_auth_superdesk'), [
            'auth_superdesk' => [
                'session_id' => '123456789',
                'token' => 'test_token',
            ],
        ]);
        self::assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testGenerateAuthenticationUrl()
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
        $apiKey = $content['token']['api_key'];
        self::assertTrue(is_string($apiKey));

        $client = static::createClient([], [
            'HTTP_Authorization' => $apiKey,
        ]);

        $client->request('POST', $this->router->generate('swp_api_auth_url'));
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertNotContains('livesite_editor', $content['url']);

        $client->request('POST', $this->router->generate('swp_api_auth_url', ['intention' => 'livesite_editor']));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('token', $content);
        self::assertArrayHasKey('url', $content);
        self::assertContains('livesite_editor', $content['url']);

        $client = static::createClient();
        $client->request('GET', $content['url']);
        self::assertEquals(302, $client->getResponse()->getStatusCode());
        $cookies = $client->getResponse()->headers->getCookies();
        self::assertCount(2, $cookies);
        self::assertEquals('activate_livesite_editor', $cookies[0]->getName());
        self::assertEquals('append_livesite_editor_scripts', $cookies[1]->getName());
        $client->followRedirect();
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client = static::createClient([], [
            'HTTP_Authorization' => 'wrong_key',
        ]);
        $client->request('POST', $this->router->generate('swp_api_auth_url', ['intention' => 'livesite_editor']));
        self::assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
