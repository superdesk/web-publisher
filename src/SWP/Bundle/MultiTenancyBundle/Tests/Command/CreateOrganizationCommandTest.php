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

use SWP\Bundle\MultiTenancyBundle\Command\CreateOrganizationCommand;
use SWP\Component\MultiTenancy\Factory\OrganizationFactoryInterface;
use SWP\Component\MultiTenancy\Model\Organization;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

class CreateOrganizationCommandTest extends \PHPUnit_Framework_TestCase
{
    private $commandTester;

    private $command;

    /**
     * @var QuestionHelper
     */
    private $question;

    public function setUp()
    {
        $application = new Application();
        $application->add(new CreateOrganizationCommand());
        $this->command = $application->get('swp:organization:create');
        $this->question = $this->command->getHelper('question');
    }

    /**
     * @covers \SWP\Bundle\MultiTenancyBundle\Command\CreateOrganizationCommand
     */
    public function testExecuteWhenCreatingNewOrganization()
    {
        $organization = new Organization();
        $organization->setCode('123456');
        $this->command->setContainer($this->getMockContainer(null, $organization, 'Test'));
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->setInputs(['Test']);
        $this->commandTester->execute(['command' => $this->command->getName()]);

        $this->assertEquals(
            'Please enter name:Organization Test (code: 123456) has been created and enabled!',
            trim($this->commandTester->getDisplay())
        );
    }

    /**
     * @covers \SWP\Bundle\MultiTenancyBundle\Command\CreateOrganizationCommand
     */
    public function testExecuteWhenCreatingDefaultOrganization()
    {
        $organization = new Organization();
        $organization->setCode('123456');
        $this->command->setContainer($this->getMockContainer(null, $organization));
        $this->commandTester = new CommandTester($this->command);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            '--default' => true,
        ]);

        $this->assertEquals(
            'Organization default (code: 123456) has been created and enabled!',
            trim($this->commandTester->getDisplay())
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @covers \SWP\Bundle\MultiTenancyBundle\Command\CreateOrganizationCommand
     */
    public function testExecuteWhenDefaultTenantExists()
    {
        $organization = new Organization();
        $organization->setCode('123456');
        $this->command->setContainer($this->getMockContainer($organization));
        $this->commandTester = new CommandTester($this->command);

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            '--default' => true,
        ]);
    }

    /**
     * @covers \SWP\Bundle\MultiTenancyBundle\Command\CreateOrganizationCommand
     */
    public function testExecuteDisabledOrganization()
    {
        $organization = new Organization();
        $organization->setCode('123456');
        $this->command->setContainer($this->getMockContainer(null, $organization, 'Example'));
        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->setInputs(['Example']);
        $this->commandTester->execute([
            'command' => $this->command->getName(),
            '--disabled' => true,
        ]);

        $this->assertEquals(
            'Please enter name:Organization Example (code: 123456) has been created and disabled!',
            trim($this->commandTester->getDisplay())
        );
    }

    private function getMockContainer($mockOrganization = null, $mockedOrganizationInFactory = null, $name = OrganizationInterface::DEFAULT_NAME)
    {
        $mockRepo = $this->getMockBuilder(OrganizationRepositoryInterface::class)
            ->getMock();

        $mockRepo->expects($this->any())
            ->method('findOneByName')
            ->with($name)
            ->willReturn($mockOrganization);

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

        $mockFactory = $this->getMockBuilder(OrganizationFactoryInterface::class)
            ->getMock();

        $mockFactory->expects($this->any())
            ->method('createWithCode')
            ->willReturn($mockedOrganizationInFactory);

        $mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->getMock();

        $mockContainer->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['swp.object_manager.organization', 1, $mockDoctrine],
                ['swp.repository.organization', 1, $mockRepo],
                ['swp.factory.organization', 1, $mockFactory],
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
