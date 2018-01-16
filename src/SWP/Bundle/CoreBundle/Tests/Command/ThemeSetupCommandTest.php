<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
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

use SWP\Bundle\CoreBundle\Command\ThemeSetupCommand;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ThemeSetupCommandTest extends WebTestCase
{
    private $commandTester;

    private $command;

    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();
        $this->loadCustomFixtures(['tenant']);

        $this->command = self::createCommand();
        $this->commandTester = $this->createCommandTester();
    }

    protected static function createCommand()
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $application = new Application($kernel);
        $application->add(new ThemeSetupCommand());

        return $application->find('swp:theme:install');
    }

    protected function createCommandTester()
    {
        $command = self::createCommand();

        return new CommandTester($command);
    }

    /**
     * @covers \SWP\Bundle\CoreBundle\Command\ThemeSetupCommand
     * @covers \SWP\Bundle\CoreBundle\Command\ThemeSetupCommand::execute
     */
    public function testExecute()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123abc',
                'theme_dir' => __DIR__.'/../Fixtures/themes/123abc/theme_test',
                '--force' => true,
            ]
        );

        self::assertContains('Theme has been installed successfully!', $this->commandTester->getDisplay());
    }

    public function testExecuteWhenDirectoryNotValid()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123abc',
                'theme_dir' => 'fake/dir',
                '--force' => true,
            ]
        );

        $this->assertContains(
            'Directory "fake/dir" does not exist or it is not a directory!',
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWhenFailure()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123abc',
                'theme_dir' => '/',
                '--force' => true,
            ]
        );

        $this->assertContains(
            'Theme could not be installed!',
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithActivation()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123abc',
                'theme_dir' => __DIR__.'/../Fixtures/themes/123abc/theme_test',
                '--force' => true,
                '--activate' => true,
            ]
        );

        $this->assertContains(
            'Theme has been installed successfully!',
            $this->commandTester->getDisplay()
        );

        $this->assertContains(
            'Theme was activated!',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @expectedException \SWP\Component\MultiTenancy\Exception\TenantNotFoundException
     */
    public function testExecuteWhenTenantNotFound()
    {
        $this->commandTester->execute(
            [
                'tenant' => '111',
                'theme_dir' => '/',
                '--force' => true,
            ]
        );
    }
}
