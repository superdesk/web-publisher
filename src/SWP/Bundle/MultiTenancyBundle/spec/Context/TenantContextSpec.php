<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\MultiTenancyBundle\Context;

use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Resolver\TenantResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class TenantContextSpec extends ObjectBehavior
{
    public function let(
        TenantResolverInterface $tenantResolver,
        RequestStack $requestStack
    ) {
        $this->beConstructedWith($tenantResolver, $requestStack);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\MultiTenancyBundle\Context\TenantContext');
    }

    public function it_implements_tenant_context_interface()
    {
        $this->shouldImplement('SWP\Component\MultiTenancy\Context\TenantContextInterface');
    }

    public function it_should_set_tenant(TenantInterface $tenant)
    {
        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('example1');
        $tenant->getName()->willReturn('example1');

        $this->setTenant($tenant)->shouldBeNull();
    }

    public function it_should_resolve_tenant_from_request(
        $requestStack,
        Request $request,
        $tenantResolver,
        TenantInterface $tenant
    ) {
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getHost()->shouldBeCalled()->willReturn('example.com');
        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('default');
        $tenant->getName()->willReturn('Default');
        $tenantResolver->resolve('example.com')->willReturn($tenant);

        $this->getTenant()->shouldReturn($tenant);
    }

    public function it_should_get_default_tenant_when_no_host_found(
        $tenantResolver,
        $requestStack,
        TenantInterface $tenant
    ) {
        $requestStack->getCurrentRequest()->willReturn(null);
        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('default');
        $tenant->getName()->willReturn('Default');
        $tenantResolver->resolve(null)->willReturn($tenant);

        $this->getTenant()->shouldReturn($tenant);
    }

    public function it_should_get_tenant(TenantInterface $tenant)
    {
        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('example1');
        $tenant->getName()->willReturn('example1');

        $this->setTenant($tenant)->shouldBeNull();
        $this->getTenant()->shouldEqual($tenant);
    }
}
