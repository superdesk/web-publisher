<?php

/**
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\FixturesBundleBundle\Tests\Command;

use SWP\Bundle\FixturesBundle\Command\ThemeSetupCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ThemeSetupCommandTest extends KernelTestCase
{
    const DELETED_MSG_REGEXP = '/Theme "theme_command_test" has been deleted successfully!/';
    const SUCCESS_MSG_REGEXP = '/Theme "theme_command_test" has been setup successfully!/';

    private $commandTester;
    private $command;

    public function setUp()
    {
        $this->command = self::createCommand();
        $this->commandTester = self::createCommandTester();
    }

    protected static function createCommand()
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $application = new Application($kernel);
        $application->add(new ThemeSetupCommand());

        return $application->find('theme:setup');
    }

    protected static function createCommandTester()
    {
        return  new CommandTester(self::createCommand());
    }

    public static function tearDownAfterClass()
    {
        self::createCommandTester()->execute(
            [
                'name'     => 'theme_command_test',
                '--force'  => true,
                '--delete' => true,
            ]
        );
    }

    /**
     * @covers SWP\Bundle\FixturesBundle\Command\ThemeSetupCommand
     * @covers SWP\Bundle\FixturesBundle\Command\ThemeSetupCommand::execute
     */
    public function testExecute()
    {
        $this->commandTester->execute(
            [
                'name'    => 'theme_command_test',
                '--force' => true,
            ]
        );

        $stub = $this->getMock('Symfony\Component\Filesystem\Filesystem', ['mirror']);
        $stub->expects($this->at(0))
            ->method('mirror')
            ->with('/some/source/dir', '/some/target/dir')
            ->will($this->returnValue(null));

        $this->assertNull($stub->mirror('/some/source/dir', '/some/target/dir'));

        $this->assertRegExp(
            '/Theme "theme_command_test" has been setup successfully!/',
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithThemeName()
    {
        $this->commandTester->execute(
            [
                'name'    => 'theme_command_test',
                '--force' => true,
            ]
        );

        $this->assertRegExp(
            self::SUCCESS_MSG_REGEXP,
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithAskConfirmation()
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', ['ask']);
        $dialog->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true)); //confirm yes

        $this->command->getHelperSet()->set($dialog, 'question');
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(
            [
                'name' => 'theme_command_test',
            ]
        );

        $this->assertRegExp(
            self::SUCCESS_MSG_REGEXP,
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithAskConfirmationOnDelete()
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', ['ask']);
        $dialog->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true)); //confirm yes

        $this->command->getHelperSet()->set($dialog, 'question');
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(
            [
                'name'     => 'theme_command_test',
                '--delete' => true,
            ]
        );

        $this->assertRegExp(
            self::DELETED_MSG_REGEXP,
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithOnDelete()
    {
        $this->commandTester->execute(
            [
                'name'    => 'theme_command_test',
                '--force' => true,
            ]
        );

        $this->commandTester->execute(
            [
                'name'     => 'theme_command_test',
                '--delete' => true,
                '--force'  => true,
            ]
        );

        $this->assertRegExp(
            self::DELETED_MSG_REGEXP,
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithAskNoOnDelete()
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', ['ask']);
        $dialog->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(false)); //confirm no

        $this->command->getHelperSet()->set($dialog, 'question');
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(
            [
                'name'     => 'theme_command_test',
                '--delete' => true,
            ]
        );

        $this->assertSame('', $this->commandTester->getDisplay());
    }

    public function testExecuteWithAskNo()
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', ['ask']);
        $dialog->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(false)); //confirm no

        $this->command->getHelperSet()->set($dialog, 'question');
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(
            [
                'name' => 'theme_command_test',
            ]
        );

        $this->assertSame('', $this->commandTester->getDisplay());
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteWhenException()
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', ['ask']);
        $dialog->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true)); //confirm yes

        $this->command->getHelperSet()->set($dialog, 'question');
        $this->commandTester = new CommandTester($this->command);

        $stub = $this->getMock('Symfony\Component\Filesystem\Filesystem', ['mirror']);
        $stub->expects($this->at(0))
            ->method('mirror')
            ->with('/some/fake/source/dir', '/some/target/dir')
            ->will($this->throwException(new \Exception()));

        $this->commandTester->execute(
            [
                'name' => 'theme_command_test',
            ]
        );

        $this->assertNull($stub->mirror('/some/fake/source/dir', '/some/target/dir'));
    }
}
