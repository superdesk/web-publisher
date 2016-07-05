<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\Tests\Command;

use SWP\Bundle\MultiTenancyBundle\Command\CreateTenantCommand;
use SWP\Component\MultiTenancy\Factory\TenantFactory;
use SWP\Component\MultiTenancy\Model\Tenant;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateTenantCommandTest extends \PHPUnit_Framework_TestCase
{
    private $commandTester;
    private $command;
    private $dialog;
    private $factory;

    public function setUp()
    {
        $application = new Application();
        $application->add(new CreateTenantCommand());
        $this->command = $application->get('swp:tenant:create');
        $this->dialog = $this->command->getHelper('dialog');
        $this->factory = new TenantFactory('SWP\Component\MultiTenancy\Model\Tenant');
    }

    /**
     * @covers SWP\Bundle\MultiTenancyBundle\Command\CreateTenantCommand
     */
    public function testExecuteWhenCreatingNewTenant()
    {
        $this->dialog->setInputStream($this->getInputStream("subdomain\nTest\n"));
        $this->command->setContainer($this->getMockContainer(null, 'subdomain'));
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(['command' => $this->command->getName()]);

        $this->assertRegExp(
            '/Please enter subdomain:Please enter name:Tenant Test has been created and enabled!/',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @covers SWP\Bundle\MultiTenancyBundle\Command\CreateTenantCommand
     */
    public function testExecuteWhenCreatingDefaultTenant()
    {
        $this->command->setContainer($this->getMockContainer());
        $this->commandTester = new CommandTester($this->command);

        $this->commandTester->execute([
            'command'   => $this->command->getName(),
            '--default' => true,
        ]);

        $this->assertRegExp(
            '/Tenant Default tenant has been created and enabled!/',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @covers SWP\Bundle\MultiTenancyBundle\Command\CreateTenantCommand
     */
    public function testExecuteWhenDefaultTenantExists()
    {
        $mockTenant = $this->getMockBuilder('SWP\Component\MultiTenancy\Model\TenantInterface')
            ->getMock();

        $this->command->setContainer($this->getMockContainer($mockTenant));
        $this->commandTester = new CommandTester($this->command);

        $this->commandTester->execute([
            'command'   => $this->command->getName(),
            '--default' => true,
        ]);
    }

    /**
     * @covers SWP\Bundle\MultiTenancyBundle\Command\CreateTenantCommand
     */
    public function testExecuteDisabledTenant()
    {
        $this->dialog->setInputStream($this->getInputStream("example\nExample\n"));
        $this->command->setContainer($this->getMockContainer(null, 'example'));
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute([
            'command'    => $this->command->getName(),
            '--disabled' => true,
        ]);

        $this->assertRegExp(
            '/Please enter subdomain:Please enter name:Tenant Example has been created and disabled!/',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @param null   $mockTenant
     * @param string $subdomain
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockContainer($mockTenant = null, $subdomain = 'default')
    {
        $mockRepo = $this->getMockBuilder('SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface')
            ->getMock();

        $mockRepo->expects($this->any())
            ->method('findBySubdomain')
            ->with($subdomain)
            ->willReturn($mockTenant);

        $mockDoctrine = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mockDoctrine->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null));
        $mockDoctrine->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));

        $mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->getMock();

        $mockContainer->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['doctrine.orm.entity_manager', 1, $mockDoctrine],
                ['swp_multi_tenancy.tenant_repository', 1, $mockRepo],
                ['swp_multi_tenancy.factory.tenant', 1, $this->factory],
            ]));

        return $mockContainer;
    }

    /**
     * @param $input
     *
     * @return resource
     */
    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fwrite($stream, $input);
        rewind($stream);

        return $stream;
    }
}
