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
use function Webmozart\Assert\Tests\StaticAnalysis\validArrayKey;

class UserControllerTest extends WebTestCase
{
    /**
     * @var object|\Symfony\Bundle\FrameworkBundle\Routing\Router|null
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

    public function testUserRolesModifications()
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
        self::assertCount(1, $content['roles']);
        self::assertEquals('ROLE_USER', $content['roles'][0]);

        $client->request('PATCH', $this->router->generate('swp_api_user_promote_user', ['id' => 1]), [
            'roles' => 'ROLE_ADMIN,ROLE_EDITOR',
        ]);
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(3, $content['roles']);
        self::assertEquals('ROLE_ADMIN', $content['roles'][1]);
        self::assertEquals('ROLE_EDITOR', $content['roles'][2]);
        self::assertEquals('ROLE_USER', $content['roles'][0]);

        $client->request('PATCH', $this->router->generate('swp_api_user_demote_user', ['id' => 1]), [
            'roles' => 'ROLE_ADMIN',
        ]);
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertCount(2, $content['roles']);
        self::assertEquals('ROLE_EDITOR', $content['roles'][1]);
        self::assertEquals('ROLE_USER', $content['roles'][0]);

        $client->request('PATCH', $this->router->generate('swp_api_user_promote_user', ['id' => 1]), [
            'roles' => 'ROLE_ADMIN, ROLE_TEST',
        ]);
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('ROLE_TEST', $content['roles'][3]);
    }
}
