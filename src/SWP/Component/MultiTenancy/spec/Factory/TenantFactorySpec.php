<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\MultiTenancy\Factory;

use PhpSpec\ObjectBehavior;
use SWP\Component\Common\Generator\GeneratorInterface;
use SWP\Component\MultiTenancy\Factory\TenantFactory;
use SWP\Component\MultiTenancy\Factory\TenantFactoryInterface;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * @mixin TenantFactory
 */
class TenantFactorySpec extends ObjectBehavior
{
    public function let(
        FactoryInterface $factory,
        GeneratorInterface $generator,
        OrganizationRepositoryInterface $organizationRepository
    ) {
        $this->beConstructedWith($factory, $generator, $organizationRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantFactory::class);
    }

    public function it_implements_tenant_factory_interface()
    {
        $this->shouldImplement(TenantFactoryInterface::class);
    }

    public function it_creates_a_new_tenant_with_code(
        FactoryInterface $factory,
        GeneratorInterface $generator,
        TenantInterface $tenant
    ) {
        $factory->create()->willReturn($tenant);
        $generator->generate(6)->willReturn('123456');
        $tenant->setCode('123456')->shouldBeCalled();

        $this->create()->shouldReturn($tenant);
    }

    public function it_creates_a_new_tenant_for_organization(
        FactoryInterface $factory,
        GeneratorInterface $generator,
        TenantInterface $tenant,
        OrganizationInterface $organization,
        OrganizationRepositoryInterface $organizationRepository
    ) {
        $organizationRepository->findOneByCode('123456')->willReturn($organization);
        $factory->create()->willReturn($tenant);
        $generator->generate(6)->willReturn('123456');
        $tenant->setCode('123456')->shouldBeCalled();
        $tenant->setOrganization($organization)->shouldBeCalled();

        $this->createForOrganization('123456')->shouldReturn($tenant);
    }

    public function it_throws_an_exception(
        FactoryInterface $factory,
        GeneratorInterface $generator,
        TenantInterface $tenant,
        OrganizationInterface $organization,
        OrganizationRepositoryInterface $organizationRepository
    ) {
        $organizationRepository->findOneByCode('123456')->willReturn(null);
        $factory->create()->shouldNotBeCalled();

        $generator->generate(6)->shouldNotBeCalled();
        $tenant->setCode('123456')->shouldNotBeCalled();
        $tenant->setOrganization($organization)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('createForOrganization', ['123456']);
    }
}
