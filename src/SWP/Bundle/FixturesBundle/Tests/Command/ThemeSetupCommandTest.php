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

use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\FixturesBundle\Command\ThemeSetupCommand;
use SWP\Bundle\WebRendererBundle\Doctrine\ODM\PHPCR\Tenant;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class ThemeSetupCommandTest extends KernelTestCase
{
    const DELETED_MSG_REGEXP = '/Theme "theme_command_test" has been deleted successfully!/';
    const SUCCESS_MSG_REGEXP = '/Theme "theme_command_test" has been setup successfully!/';

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

        return $application->find('theme:setup');
    }

    protected function createCommandTester()
    {
        $command = self::createCommand();
        $tenant = new Tenant();
        $tenant->setCode('123456');
        $command->setContainer($this->getMockContainer($tenant));

        return  new CommandTester($command);
    }

    private function getMockContainer($mockTenant = null)
    {
        $mockRepo = $this->getMock(TenantRepositoryInterface::class);

        $mockRepo->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => TenantInterface::DEFAULT_TENANT_NAME])
            ->will($this->returnValue($mockTenant));

        $mockDoctrine = $this
            ->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockDoctrine->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null));
        $mockDoctrine->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));

        $stubKernel = $this->getMock(KernelInterface::class);
        $stubKernel->expects($this->at(0))
            ->method('getRootDir')
            ->will($this->returnValue(sys_get_temp_dir().'/'.Kernel::VERSION));

        $stubKernel->expects($this->once())
            ->method('locateResource')
            ->with('@SWPFixturesBundle/Resources/themes/theme_command_test')
            ->will($this->returnValue(__DIR__.'/../../Resources/themes/theme_command_test'));

        $mockContainer = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $mockContainer->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['doctrine_phpcr.odm.document_manager', 1, $mockDoctrine],
                ['swp.repository.tenant', 1, $mockRepo],
                ['kernel', 1, $stubKernel],
            ]));

        return $mockContainer;
    }

    /**
     * @covers SWP\Bundle\FixturesBundle\Command\ThemeSetupCommand
     * @covers SWP\Bundle\FixturesBundle\Command\ThemeSetupCommand::execute
     */
    public function testExecute()
    {
        $this->commandTester->execute(
            [
                'name' => 'theme_command_test',
                '--force' => true,
            ]
        );

        $this->assertRegExp(
            self::SUCCESS_MSG_REGEXP,
            $this->commandTester->getDisplay()
        );
    }
}
