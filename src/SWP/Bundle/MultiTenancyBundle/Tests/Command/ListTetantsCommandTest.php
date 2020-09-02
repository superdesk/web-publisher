<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use SWP\Bundle\MultiTenancyBundle\Command\ListTenantsCommand;
use SWP\Component\MultiTenancy\Model\Organization;
use SWP\Component\MultiTenancy\Model\Tenant;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListTetantsCommandTest extends TestCase
{
    const ORGANIZATION_CODE = '123456';

    private $commandTester;

    private $command;

    private function setupCommand($container)
    {
        $application = new Application();
        $application->add(new ListTenantsCommand($container->get('swp.repository.organization'), $container->get('swp.repository.tenant')));
        $this->command = $application->get('swp:tenant:list');
    }

    /**
     * @covers \SWP\Bundle\MultiTenancyBundle\Command\ListTenantsCommand
     */
    public function testExecuteWhenNoTenants()
    {
        $tenant = new Tenant();
        $tenant->setCode('123abc');
        $container = $this->getMockContainer(new Organization());
        $this->setupCommand($container);

        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(['command' => $this->command->getName()]);

        $this->assertContains(
            'There are no tenants defined.',
            trim($this->commandTester->getDisplay())
        );
    }

    /**
     * @covers \SWP\Bundle\MultiTenancyBundle\Command\ListTenantsCommand
     */
    public function testListWithTenants()
    {
        $tenant = new Tenant();
        $tenant->setCode('123abc');
        $tenant->setId('1');
        $tenant->setName('Test Tenant');
        $tenant->setCreatedAt(new \DateTime('2017-02-20 15:19:55'));
        $organization = new Organization();
        $organization->setCode('123456');
        $organization->setName('Test Organization');
        $organization->addTenant($tenant);
        $tenant->setOrganization($organization);

        $container = $this->getMockContainer($organization);
        $this->setupCommand($container);

        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(['command' => $this->command->getName()]);

        $result = <<<'EOF'
List of all available tenants:
+----+--------+-------------+--------+-----------+------------+------------+---------------------+----------------------------------+
| Id | Code   | Name        | Domain | Subdomain | Is active? | Theme Name | Created at          | Organization                     |
+----+--------+-------------+--------+-----------+------------+------------+---------------------+----------------------------------+
| 1  | 123abc | Test Tenant |        |           | yes        |            | 2017-02-20 15:19:55 | Test Organization (code: 123456) |
+----+--------+-------------+--------+-----------+------------+------------+---------------------+----------------------------------+
EOF;

        $this->assertEquals($result, trim($this->commandTester->getDisplay()));
    }

    /**
     * @covers \SWP\Bundle\MultiTenancyBundle\Command\CreateTenantCommand
     */
    public function testListWithTenantsAndOrganizationOption()
    {
        $tenant = new Tenant();
        $tenant->setCode('123abc');
        $tenant->setId('1');
        $tenant->setName('Test Tenant');
        $tenant->setCreatedAt(new \DateTime('2017-02-20 15:19:55'));
        $organization = new Organization();
        $organization->setCode('123456');
        $organization->setName('Test Organization');
        $organization->addTenant($tenant);
        $tenant->setOrganization($organization);

        $container = $this->getMockContainer($organization);
        $this->setupCommand($container);

        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(['command' => $this->command->getName()]);

        $result = <<<'EOF'
List of all available tenants:
+----+--------+-------------+--------+-----------+------------+------------+---------------------+----------------------------------+
| Id | Code   | Name        | Domain | Subdomain | Is active? | Theme Name | Created at          | Organization                     |
+----+--------+-------------+--------+-----------+------------+------------+---------------------+----------------------------------+
| 1  | 123abc | Test Tenant |        |           | yes        |            | 2017-02-20 15:19:55 | Test Organization (code: 123456) |
+----+--------+-------------+--------+-----------+------------+------------+---------------------+----------------------------------+
EOF;

        $this->assertEquals($result, trim($this->commandTester->getDisplay()));

        $tenant2 = new Tenant();
        $tenant2->setCode('345def');
        $tenant2->setId('2');
        $tenant2->setName('Test Tenant 2');
        $tenant2->setCreatedAt(new \DateTime('2017-02-20 15:19:55'));
        $organization2 = new Organization();
        $organization2->setCode('789012');
        $organization2->setName('Test Organization2');
        $organization2->addTenant($tenant2);
        $tenant2->setOrganization($organization2);

        $container = $this->getMockContainer($organization2);
        $this->setupCommand($container);

        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute(['command' => $this->command->getName(), '-o' => '123456']);
        $result = <<<'EOF'
There are no tenants defined.
EOF;
        $this->assertEquals($result, trim($this->commandTester->getDisplay()));

        $this->commandTester->execute(['command' => $this->command->getName()]);
        $result = <<<'EOF'
List of all available tenants:
+----+--------+---------------+--------+-----------+------------+------------+---------------------+-----------------------------------+
| Id | Code   | Name          | Domain | Subdomain | Is active? | Theme Name | Created at          | Organization                      |
+----+--------+---------------+--------+-----------+------------+------------+---------------------+-----------------------------------+
| 2  | 345def | Test Tenant 2 |        |           | yes        |            | 2017-02-20 15:19:55 | Test Organization2 (code: 789012) |
+----+--------+---------------+--------+-----------+------------+------------+---------------------+-----------------------------------+
EOF;
        $this->assertEquals($result, trim($this->commandTester->getDisplay()));
    }

    private function getMockContainer($mockOrganization = null)
    {
        $mockRepoOrganization = $this->getMockBuilder(OrganizationRepositoryInterface::class)
            ->getMock();

        $mockRepoOrganization->expects($this->any())
            ->method('findOneByCode')
            ->with(self::ORGANIZATION_CODE)
            ->willReturn($mockOrganization);

        $mockRepo = $this->getMockBuilder(TenantRepositoryInterface::class)
            ->getMock();

        $mockRepo->expects($this->any())
            ->method('findAll')
            ->willReturn($mockOrganization->getTenants());

        $mockContainer = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        $mockContainer->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['swp.repository.tenant', 1, $mockRepo],
                ['swp.repository.organization', 1, $mockRepoOrganization],
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
