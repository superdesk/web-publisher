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
namespace SWP\FixturesBundleBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SWP\FixturesBundle\Command\ThemeSetupCommand;

class ThemeSetupCommandTest extends KernelTestCase
{
    const DELETED_MSG_REGEXP = '/Theme "theme_1" has been deleted successfully!/';
    const SUCCESS_MSG_REGEXP = '/Theme "theme_1" has been setup successfully!/';

    private $commandTester;
    private $command;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $application = new Application($kernel);
        $application->add(new ThemeSetupCommand());

        $this->command = $application->find('theme:setup');
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @covers SWP\FixturesBundle\Command\ThemeSetupCommand
     * @covers SWP\FixturesBundle\Command\ThemeSetupCommand::execute
     */
    public function testExecute()
    {
        $this->commandTester->execute(
            array(
                '--force' => true,
            )
        );

        $this->assertRegExp('/theme_1/', $this->commandTester->getDisplay());
    }

    public function testExecuteWithThemeName()
    {
        $this->commandTester->execute(
            array(
                'name' => 'theme',
                '--force' => true,
            )
        );

        $this->assertRegExp(
            '/Theme "theme" has been setup successfully!/',
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithAskConfirmation()
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));
        $dialog->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true)); //confirm yes

        $this->command->getHelperSet()->set($dialog, 'question');
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(
            array(
                'name' => 'theme_1',
            )
        );

        $this->assertRegExp(
            self::SUCCESS_MSG_REGEXP,
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithAskConfirmationOnDelete()
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));
        $dialog->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true)); //confirm yes

        $this->command->getHelperSet()->set($dialog, 'question');
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(
            array(
                'name' => 'theme_1',
                '--delete' => true,
            )
        );

        $this->assertRegExp(
            self::DELETED_MSG_REGEXP,
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithOnDelete()
    {
        $this->commandTester->execute(
            array(
                'name' => 'theme_1',
                '--delete' => true,
                '--force' => true,
            )
        );

        $this->assertRegExp(
            self::DELETED_MSG_REGEXP,
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithAskNoOnDelete()
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));
        $dialog->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(false)); //confirm no

        $this->command->getHelperSet()->set($dialog, 'question');
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(
            array(
                'name' => 'theme_1',
                '--delete' => true,
            )
        );

        $this->assertSame('', $this->commandTester->getDisplay());
    }

    public function testExecuteWithAskNo()
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));
        $dialog->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(false)); //confirm no

        $this->command->getHelperSet()->set($dialog, 'question');
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(
            array(
                'name' => 'theme_1',
            )
        );

        $this->assertSame('', $this->commandTester->getDisplay());
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteWhenException()
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));
        $dialog->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true)); //confirm yes

        $this->command->getHelperSet()->set($dialog, 'question');
        $this->commandTester = new CommandTester($this->command);

        $stub = $this->getMock('Symfony\Component\Filesystem\Filesystem', array('mirror'));
        $stub->expects($this->at(0))
            ->method('mirror')
            ->with('/some/fake/source/dir', '/some/target/dir')
            ->will($this->throwException(new \Exception()));

        $this->commandTester->execute(
            array(
                'name' => 'fake_theme_not_existing',
            )
        );

        $this->assertNull($stub->mirror('/some/fake/source/dir', '/some/target/dir'));
    }
}
