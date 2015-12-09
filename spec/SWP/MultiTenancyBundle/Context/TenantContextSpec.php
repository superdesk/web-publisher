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
namespace spec\SWP\MultiTenancyBundle\Context;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use SWP\MultiTenancyBundle\Model\TenantInterface;
use SWP\MultiTenancyBundle\Resolver\TenantResolverInterface;

class TenantContextSpec extends ObjectBehavior
{
    public function let(TenantResolverInterface $tenantResolver, RequestStack $requestStack)
    {
        $this->beConstructedWith($tenantResolver, $requestStack);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\MultiTenancyBundle\Context\TenantContext');
    }

    public function it_implements_tenant_resolver_interface()
    {
        $this->shouldImplement('SWP\MultiTenancyBundle\Context\TenantContextInterface');
    }

    public function it_should_set_tenant(TenantInterface $tenant)
    {
        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('example1');
        $tenant->getName()->willReturn('example1');

        $this->setTenant($tenant);
    }

    public function it_should_get_resolved_tenant(TenantInterface $tenant, Request $request, $tenantResolver, $requestStack)
    {
        $host = 'example1.domain.com';
        $request->getHost()->willReturn($host);
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getHost()->shouldBeCalled();

        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('example1');
        $tenant->getName()->willReturn('example1');

        $tenantResolver->resolve($host)->shouldBeCalled()->willReturn($tenant);

        $this->getTenant();
    }

    public function it_should_get_cached_tenant(TenantInterface $tenant, Request $request, $tenantResolver, $requestStack)
    {
        $host = 'example1.domain.com';
        $request->getHost()->willReturn($host);
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getHost()->shouldBeCalled();

        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('example1');
        $tenant->getName()->willReturn('example1');

        $this->setTenant($tenant);

        $tenantResolver->resolve($host)->shouldNotBeCalled();

        $this->getTenant();
    }
}
