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

class UserControllerTest extends WebTestCase
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

    public function testUserRolesModifications()
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
        self::assertCount(1, $content['roles']);
        self::assertEquals('ROLE_USER', $content['roles'][0]);

        $client->request('PATCH', $this->router->generate('swp_api_user_promote_user', ['id' => 1]), [
            'swp_api_user_promote_user' => [
                'roles' => 'ROLE_ADMIN,ROLE_EDITOR',
            ],
        ]);
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(3, $content['roles']);
        self::assertEquals('ROLE_ADMIN', $content['roles'][0]);
        self::assertEquals('ROLE_EDITOR', $content['roles'][1]);
        self::assertEquals('ROLE_USER', $content['roles'][2]);

        $client->request('PATCH', $this->router->generate('swp_api_user_demote_user', ['id' => 1]), [
            'swp_api_user_promote_user' => [
                'roles' => 'ROLE_ADMIN',
            ],
        ]);
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(2, $content['roles']);
        self::assertEquals('ROLE_EDITOR', $content['roles'][0]);
        self::assertEquals('ROLE_USER', $content['roles'][1]);

        $client->request('PATCH', $this->router->generate('swp_api_user_promote_user', ['id' => 1]), [
            'swp_api_user_promote_user' => [
                'roles' => 'ROLE_ADMIN, ROLE_TEST',
            ],
        ]);
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('ROLE_TEST', $content['roles'][2]);
    }
}
