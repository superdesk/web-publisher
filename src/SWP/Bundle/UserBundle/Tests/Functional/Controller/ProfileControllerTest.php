<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2021 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @Copyright 2021 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Tests\Functional\Controller;

use SWP\Bundle\UserBundle\Tests\Functional\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    /**
     * @var object|\Symfony\Cmf\Component\Routing\ChainRouter|null
     */
    private $router;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->initDatabase();
        $this->router = $this->getContainer()->get('router');
    }

    public function testUserUpdate()
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_register_user'), [
            'email' => 'contact@example.com',
            'username' => 'sofab.contact',
            'plainPassword' => [
                'first' => 'testPass',
                'second' => 'testPass',
            ],
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', $this->router->generate('swp_api_user_get_user_profile', ['id' => 1]));
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('sofab.contact', $content['username']);

        $client->request('PATCH', $this->router->generate('swp_api_user_edit_user_profile', ['id' => 1]), [
            'email' => 'contact2@example.com',
            'username' => 'sofab.contact2',
            'firstName' => 'Test',
            'lastName' => 'User',
            'about' => 'About content',
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('sofab.contact2', $content['username']);
        self::assertEquals('Test', $content['first_name']);
        self::assertEquals('User', $content['last_name']);
        self::assertEquals('About content', $content['about']);

        $client->request('PATCH', $this->router->generate('swp_api_user_edit_user_profile', ['id' => 1]), [
            'about' => '',
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
