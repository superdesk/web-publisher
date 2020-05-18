<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Tests\Command;

use SWP\Bundle\CoreBundle\Command\ThemeSetupCommand;
use SWP\Bundle\FixturesBundle\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DownloadThemesFromExternalStorageCommandTest extends WebTestCase
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

    /**
     * @covers \SWP\Bundle\CoreBundle\Command\DownloadThemesFromExternalStorageCommand
     * @covers \SWP\Bundle\CoreBundle\Command\DownloadThemesFromExternalStorageCommand::execute
     */
    public function testExecute()
    {
        $this->commandTester->execute([]);

        self::assertContains('Themes were downloaded and extracted.', $this->commandTester->getDisplay());
    }

    protected static function createCommand()
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $application = new Application($kernel);
        $application->add(new ThemeSetupCommand());

        return $application->find('swp:theme:download-from-external');
    }

    protected function createCommandTester()
    {
        $command = self::createCommand();

        return new CommandTester($command);
    }
}
