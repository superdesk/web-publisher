<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Component\MultiTenancy\Resolver;

use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;

class TenantResolverSpec extends ObjectBehavior
{
    public function let(TenantRepositoryInterface $tenantRepository)
    {
        $this->beConstructedWith($tenantRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Component\MultiTenancy\Resolver\TenantResolver');
    }

    public function it_implements_tenant_resolver_interface()
    {
        $this->shouldImplement('SWP\Component\MultiTenancy\Resolver\TenantResolverInterface');
    }

    public function it_resolves_tenant_from_subdomain($tenantRepository, TenantInterface $tenant)
    {
        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('example1');
        $tenant->getName()->willReturn('example1');

        $tenantRepository->findOneBySubdomainAndDomain('example1', 'domain.com')
            ->shouldBeCalled()
            ->willReturn($tenant);

        $this->resolve('example1.domain.com')->shouldReturn($tenant);

        $tenantRepository->findOneBySubdomainAndDomain('www.example1', 'domain.com')
            ->shouldBeCalled()
            ->willReturn(null);
        $this
            ->shouldThrow(TenantNotFoundException::class)
            ->duringResolve('www.example1.domain.com');
    }

    public function it_resolves_tenant_from_www_subdomain($tenantRepository, TenantInterface $tenant)
    {
        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('www');
        $tenant->getName()->willReturn('example1');

        $tenantRepository->findOneBySubdomainAndDomain('www', 'domain.com')
            ->shouldBeCalled()
            ->willReturn($tenant);

        $this->resolve('www.domain.com')->shouldReturn($tenant);

        $tenantRepository->findOneByDomain('domain.com')
            ->shouldBeCalled()
            ->willReturn(null);
        $this
            ->shouldThrow(TenantNotFoundException::class)
            ->duringResolve('domain.com');
    }

    public function it_resolves_tenant_from_default_root_host($tenantRepository, TenantInterface $tenant)
    {
        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('default');
        $tenant->getName()->willReturn('default');

        $tenantRepository->findOneByDomain('domain.com')
            ->shouldBeCalled()
            ->willReturn($tenant);

        $this->resolve('domain.com')->shouldReturn($tenant);

        $tenantRepository->findOneBySubdomainAndDomain('www', 'domain.com')
            ->shouldBeCalled()
            ->willReturn(null);
        $this
            ->shouldThrow(TenantNotFoundException::class)
            ->duringResolve('www.domain.com');
    }
}
