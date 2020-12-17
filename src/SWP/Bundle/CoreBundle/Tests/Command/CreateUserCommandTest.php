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

namespace SWP\Bundle\CoreBundle\Tests\Command;

use SWP\Bundle\FixturesBundle\WebTestCase;

class CreateUserCommandTest extends WebTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);
    }

    public function testCommand()
    {
        $userData = [
            'username' => '123456',
            'email' => 'test23',
            'password' => 'localhost'
        ];
        $commandTester = $this->runCommand('swp:user:create', $userData, true);
        self::assertContains('Created user', $commandTester->getDisplay());
        $commandTester = $this->runCommand('swp:user:create', $userData, true);
        self::assertContains('already exists', $commandTester->getDisplay());
    }
}
