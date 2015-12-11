<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\MultiTenancyBundle\Resolver;

use PhpSpec\ObjectBehavior;
use SWP\MultiTenancyBundle\Model\TenantInterface;
use SWP\MultiTenancyBundle\Repository\TenantRepositoryInterface;

class TenantResolverSpec extends ObjectBehavior
{
    public function let(TenantRepositoryInterface $tenantRepository)
    {
        $this->beConstructedWith('domain.com', $tenantRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\MultiTenancyBundle\Resolver\TenantResolver');
    }

    public function it_implements_tenant_resolver_interface()
    {
        $this->shouldImplement('SWP\MultiTenancyBundle\Resolver\TenantResolverInterface');
    }

    public function it_should_not_allow_not_existing_tenants($tenantRepository)
    {
        $tenantRepository->findBySubdomain('example1')->shouldBeCalled();

        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->duringResolve('example1.domain.com');
    }

    public function it_resolves_tenant_from_subdomain($tenantRepository, TenantInterface $tenant)
    {
        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('example1');
        $tenant->getName()->willReturn('example1');

        $tenantRepository->findBySubdomain('example1')
            ->shouldBeCalled()
            ->willReturn($tenant);

        $this->resolve('example1.domain.com');
    }

    public function it_resolves_tenant_from_default_root_host($tenantRepository, TenantInterface $tenant)
    {
        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('default');
        $tenant->getName()->willReturn('default');

        $tenantRepository->findBySubdomain('default')
            ->shouldBeCalled()
            ->willReturn($tenant);

        $this->resolve('domain.com');
    }
}
