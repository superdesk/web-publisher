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

class RegistrationControllerTest extends WebTestCase
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

    public function testRegistrationDisabling()
    {
        $this->getContainer()->get('swp_settings.manager.settings')->set('registration_enabled', false);
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_core_register_user'));

        // Registration is disabled in tests
        self::assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
