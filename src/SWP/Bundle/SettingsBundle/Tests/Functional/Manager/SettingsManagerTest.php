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

use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Exception\InvalidScopeException;
use SWP\Bundle\SettingsBundle\Manager\SettingsManager;
use SWP\Bundle\SettingsBundle\Model\SettingsInterface;
use SWP\Bundle\SettingsBundle\Provider\SettingsProviderChain;
use SWP\Bundle\SettingsBundle\Tests\Functional\Model\User;
use SWP\Bundle\SettingsBundle\Tests\Functional\WebTestCase;

class SettingsManagerTest extends WebTestCase
{
    public function testDebugConstruct()
    {
        $this->createService();
    }

    public function testGetAllSettingsFromConfiguration()
    {
        $this->initDatabase();
        $settingsManager = $this->createService();

        self::assertCount(4, $settingsManager->all());
        self::assertEquals(null, $settingsManager->get('first_setting'));
        self::assertEquals('default', $settingsManager->get('first_setting', ScopeContextInterface::SCOPE_GLOBAL, null, 'default'));
        self::assertEquals(123, $settingsManager->get('second_setting'));
        self::assertEquals(null, $settingsManager->get('first_setting'));

        $owner = $this->getContainer()->get('swp_settings.context.scope')->setScopeOwner(
            ScopeContextInterface::SCOPE_USER,
            new User(1, 'publisher', 'testpass')
        );

        self::assertEquals('sdfgesgts4tgse5tdg4t', $settingsManager->get('third_setting', ScopeContextInterface::SCOPE_USER, $owner));
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
        self::assertInstanceOf(SettingsInterface::class, $settingsManager->set('first_setting', 'value'));
        self::assertEquals('value', $settingsManager->get('first_setting'));
        self::assertTrue($settingsManager->clear('first_setting'));
        self::assertEquals(null, $settingsManager->get('first_setting'));

        $owner = $this->getContainer()->get('swp_settings.context.scope')->setScopeOwner(ScopeContextInterface::SCOPE_USER, new User(1, 'publisher', 'testpass'));
        self::assertInstanceOf(SettingsInterface::class, $settingsManager->set('third_setting', '123456', ScopeContextInterface::SCOPE_USER, $owner));
        self::assertEquals('123456', $settingsManager->get('third_setting', ScopeContextInterface::SCOPE_USER, $owner));
    }

    private function createService()
    {
        return new SettingsManager(
            $this->getContainer()->get('doctrine.orm.entity_manager'),
            $this->getContainer()->get(SettingsProviderChain::class),
            $this->getContainer()->get('swp.repository.settings'),
            $this->getContainer()->get('swp.factory.settings'),
            $this->getContainer()->get('swp_settings.context.scope')
        );
    }
}
