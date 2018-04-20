<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\Tests\Functional\Manager;

use SWP\Bundle\SettingsBundle\Tests\Functional\WebTestCase;

class SettingsControllerTest extends WebTestCase
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

    public function testListSettingsApi()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_settings_list'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);

        self::assertCount(4, $data);
    }

    public function testSettingsUpdate()
    {
        self::assertEquals(null, $this->getContainer()->get('swp_settings.manager.settings')->get('first_setting'));
        $client = static::createClient();
        $client->request('PATCH', $this->router->generate('swp_api_settings_update', []), [
            'settings' => [
                'name' => 'first_setting',
                'value' => 'not null',
            ],
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('not null', $data['value']);
        self::assertEquals('not null', $this->getContainer()->get('swp_settings.manager.settings')->get('first_setting'));

        $client->request('PATCH', $this->router->generate('swp_api_settings_update', []), [
            'settings' => [
                'name' => 'third_setting',
                'value' => '1234567string',
            ],
        ]);
        $this->assertEquals(500, $client->getResponse()->getStatusCode());

        $client = static::createClient([], ['PHP_AUTH_USER' => 'publisher', 'PHP_AUTH_PW' => 'testpass']);
        $client->request('PATCH', $this->router->generate('swp_api_settings_update'), [
            'settings' => [
                'name' => 'third_setting',
                'value' => '1234567string',
            ],
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals('1234567string', $data['value']);
        self::assertEquals('1234567string', $this->getContainer()->get('swp_settings.manager.settings')->get('third_setting', null));

        $client = static::createClient([], ['PHP_AUTH_USER' => 'publisher', 'PHP_AUTH_PW' => 'testpass']);
        $client->request('PATCH', $this->router->generate('swp_api_settings_update'), [
            'settings' => [
                'name' => 'third_setting',
                'value' => '1234567',
            ],
        ]);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }
}
