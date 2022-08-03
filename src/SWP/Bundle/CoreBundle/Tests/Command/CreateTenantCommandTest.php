<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Command;

use SWP\Bundle\FixturesBundle\WebTestCase;

class CreateTenantCommandTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
    }

    public function testCommand()
    {
        $commandTester = $this->runCommand('swp:tenant:create', ['organization code' => '123456', 'subdomain' => 'test23', 'domain' => 'localhost', 'name' => 'Tenant'], true);
        self::assertStringContainsString('has been created and', $commandTester->getDisplay());

        $commandTester = $this->runCommand('swp:tenant:create', ['organization code' => '123456', 'domain' => 'localhost2', 'name' => 'Tenant 2'], true);
        self::assertStringContainsString('has been created and', $commandTester->getDisplay());
    }
}
