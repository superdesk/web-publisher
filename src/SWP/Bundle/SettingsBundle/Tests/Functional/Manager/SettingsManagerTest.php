<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\Tests\Functional\Manager;

use SWP\Bundle\SettingsBundle\Exception\InvalidScopeException;
use SWP\Bundle\SettingsBundle\Manager\SettingsManager;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Bundle\SettingsBundle\Tests\Functional\WebTestCase;

class ContainerServiceTest extends WebTestCase
{
    public function testDebugConstruct()
    {
        $this->createService();
    }

    public function testGetAllSettingsFromConfiguration()
    {
        $this->initDatabase();
        $settingsManager = $this->createService();

        self::assertCount(3, $settingsManager->all());
        self::assertEquals(null, $settingsManager->get('first_setting'));
        self::assertEquals('default', $settingsManager->get('first_setting', 'global', null, 'default'));
        self::assertEquals(123, $settingsManager->get('second_setting'));
        self::assertEquals(null, $settingsManager->get('first_setting'));
        self::assertEquals('sdfgesgts4tgse5tdg4t', $settingsManager->get('third_setting', 'user'));
        self::assertEquals(['a' => 1, 'b' => 2], $settingsManager->get('fourth_setting'));
    }

    public function testGettingWrongScope()
    {
        $this->initDatabase();
        $settingsManager = $this->createService();

        $this->expectException(InvalidScopeException::class);
        $settingsManager->get('third_setting');
    }

    public function testWrongSettingName()
    {
        $this->initDatabase();
        $settingsManager = $this->createService();

        $this->expectException(\Exception::class);
        $settingsManager->get('setting');
    }

    public function testGetAndSetAndGetAndClear()
    {
        $this->initDatabase();
        $settingsManager = $this->createService();

        self::assertEquals(null, $settingsManager->get('first_setting'));
        self::assertTrue($settingsManager->set('first_setting', 'value'));
        self::assertEquals('value', $settingsManager->get('first_setting'));
        self::assertTrue($settingsManager->clear('first_setting'));
        self::assertEquals(null, $settingsManager->get('first_setting'));
    }

    public function testScopes()
    {
        $this->initDatabase();
        $settingsManager = $this->createService();

        self::assertEquals([
            SettingsManagerInterface::SCOPE_GLOBAL,
            SettingsManagerInterface::SCOPE_USER,
        ], $settingsManager->getScopes());
    }

    private function createService()
    {
        return new SettingsManager(
            $this->getContainer()->get('doctrine.orm.entity_manager'),
            $this->getContainer()->get('swp.serializer'),
            $this->getContainer()->getParameter('swp_settings.settings'),
            $this->getContainer()->get('swp.repository.settings'),
            $this->getContainer()->get('swp.factory.settings')
        );
    }
}
