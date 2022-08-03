<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Functional;

use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

final class SettingsTest extends WebTestCase
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

    public function testSettingUserFilteringPreferences()
    {
        $client = static::createClient();
        $client->request('GET', $this->router->generate('swp_api_settings_list'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('filtering_prefrences', $data[6]['name']);
        self::assertEquals('{}', $data[6]['value']);
        $client->request('PATCH', $this->router->generate('swp_api_settings_update'), [
                'name' => 'filtering_prefrences',
                'value' => '{"a": "b", "c": "d"}',
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $client->request('GET', $this->router->generate('swp_api_settings_list'));
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('{"a": "b", "c": "d"}', $data[6]['value']);
    }
}
