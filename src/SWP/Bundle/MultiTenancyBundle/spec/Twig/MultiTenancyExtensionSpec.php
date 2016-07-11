<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\MultiTenancyBundle\Twig;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\MultiTenancyBundle\Twig\MultiTenancyExtension;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;

/**
 * @mixin MultiTenancyExtension
 */
class MultiTenancyExtensionSpec extends ObjectBehavior
{
    public function let(TenantContextInterface $tenantContext)
    {
        $this->beConstructedWith($tenantContext);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\MultiTenancyBundle\Twig\MultiTenancyExtension');
    }

    public function it_is_a_twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    public function it_should_return_global_variables(TenantInterface $tenant, TenantContextInterface $tenantContext)
    {
        $tenant->getSubdomain()->willReturn('example');
        $tenant->getName()->willReturn('example tenant');
        $tenantContext->getTenant()->shouldBeCalled()->willReturn($tenant);

        $globals = [
            'organization' => $tenant,
        ];

        $this->getGlobals()->shouldReturn($globals);
    }

    public function it_should_have_a_name()
    {
        $this->getName()->shouldReturn('swp_multi_tenancy');
    }
}
