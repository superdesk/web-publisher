<?php
/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Tests\Functional\Controler;

use SWP\Bundle\UserBundle\Tests\Functional\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->router = $this->getContainer()->get('router');
    }

    public function testUserUpdate()
    {
        $client = static::createClient();
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

        self::assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_user_get_user_profile', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('sofab.contact', $content['username']);

        $client->request('PATCH', $this->router->generate('swp_api_user_edit_user_profile', ['id' => 1]), [
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
        self::assertEquals('Test', $content['first_name']);
        self::assertEquals('User', $content['last_name']);
        self::assertEquals('About content', $content['about']);
    }
}
