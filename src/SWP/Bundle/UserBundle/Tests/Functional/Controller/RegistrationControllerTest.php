<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\Tests\Functional\Controller;

use SWP\Bundle\UserBundle\Tests\Functional\WebTestCase;

class RegistrationControllerTest extends WebTestCase
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

    public function testRegistrationDisabling()
    {
        $this->getContainer()->get('swp_settings.manager.settings')->set('registration_enabled', false);
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_register_user'));

        // Registration is disabled in tests
        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
