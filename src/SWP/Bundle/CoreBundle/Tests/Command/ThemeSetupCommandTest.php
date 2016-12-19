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
use SWP\Bundle\CoreBundle\Document\Tenant;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ThemeSetupCommandTest extends KernelTestCase
{
    private $commandTester;
    private $command;

    public function setUp()
    {
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
        $tenant = new Tenant();
        $tenant->setCode('123456');
        $command->setContainer($this->getMockContainer($tenant));

        return new CommandTester($command);
    }

    private function getMockContainer($mockTenant = null, $tenantCode = '123456')
    {
        $mockRepo = $this->getMock(TenantRepositoryInterface::class);

        $mockRepo->expects($this->any())
            ->method('findOneByCode')
            ->with($tenantCode)
            ->will($this->returnValue($mockTenant));

        $mockContainer = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $mockContainer->expects($this->any())
            ->method('getParameter')
            ->with('swp.theme.configuration.default_directory')
            ->will($this->returnValue('/tmp'));

        $mockContainer->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['swp.repository.tenant', 1, $mockRepo],
            ]));

        return $mockContainer;
    }

    /**
     * @covers \SWP\Bundle\CoreBundle\Command\ThemeSetupCommand
     * @covers \SWP\Bundle\CoreBundle\Command\ThemeSetupCommand::execute
     */
    public function testExecute()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123456',
                'theme_dir' => __DIR__.'/../Fixtures/themes/123abc/theme_test',
                '--force' => true,
            ]
        );

        $this->assertContains(
            'Theme has been installed successfully!',
            $this->commandTester->getDisplay()
        );
    }

    public function testExecuteWhenDirectoryNotValid()
    {
        $this->commandTester->execute(
            [
                'tenant' => '123456',
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
                'tenant' => '123456',
                'theme_dir' => '/',
                '--force' => true,
            ]
        );

        $this->assertContains(
            'Theme could not be installed!',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @expectedException \SWP\Component\MultiTenancy\Exception\TenantNotFoundException
     */
    public function testExecuteWhenTenantNotFound()
    {
        $command = self::createCommand();
        $command->setContainer($this->getMockContainer(null, '111'));

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'tenant' => '111',
                'theme_dir' => '/',
                '--force' => true,
            ]
        );
    }
}
