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

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SWP\Bundle\FixturesBundle\Command\ThemeSetupCommand;
use Symfony\Component\Filesystem\Filesystem;

class ThemeSetupCommandTest extends KernelTestCase
{
    const DELETED_MSG_REGEXP = '/Theme "theme_testing" has been deleted successfully!/';
    const SUCCESS_MSG_REGEXP = '/Theme "theme_testing" has been setup successfully!/';

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

    public static function tearDownAfterClass()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__.'/../../../../../../app/Resources/themes/theme_testing');
    }

    /**
     * @covers SWP\Bundle\FixturesBundle\Command\ThemeSetupCommand
     * @covers SWP\Bundle\FixturesBundle\Command\ThemeSetupCommand::execute
     */
    public function testExecute()
    {
        $this->commandTester->execute(
            array(
                '--force' => true,
            )
        );

        $stub = $this->getMock('Symfony\Component\Filesystem\Filesystem', array('mirror'));
        $stub->expects($this->at(0))
            ->method('mirror')
            ->with('/some/source/dir', '/some/target/dir')
            ->will($this->returnValue(null));

        $this->assertNull($stub->mirror('/some/source/dir', '/some/target/dir'));

        $this->assertRegExp(
            '/Theme "theme_1" has been setup successfully!/',
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWithThemeName()
    {
        $this->commandTester->execute(
            array(
                'name' => 'theme_testing',
                '--force' => true,
            )
        );

        $this->assertRegExp(
            self::SUCCESS_MSG_REGEXP,
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
                'name' => 'theme_testing',
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
                'name' => 'theme_testing',
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
                'name' => 'theme_testing',
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
                'name' => 'theme_testing',
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
                'name' => 'theme_testing',
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
                'name' => 'theme_testing',
            )
        );

        $this->assertNull($stub->mirror('/some/fake/source/dir', '/some/target/dir'));
    }
}
