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

namespace spec\SWP\Component\MultiTenancy\Context;

use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\Model\TenantInterface;

class TenantContextSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Component\MultiTenancy\Context\TenantContext');
    }

    public function it_implements_tenant_resolver_interface()
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

    public function it_should_get_tenant(TenantInterface $tenant)
    {
        $tenant->getId()->willReturn(1);
        $tenant->getSubdomain()->willReturn('example1');
        $tenant->getName()->willReturn('example1');

        $this->setTenant($tenant)->shouldBeNull();
        $this->getTenant()->shouldEqual($tenant);
    }

    public function it_should_return_null()
    {
        $this->getTenant()->shouldBeNull();
    }
}
