<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Component\MultiTenancy\Provider;

use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

class TenantProviderSpec extends ObjectBehavior
{
    public function let(TenantRepositoryInterface $tenantRepository)
    {
        $this->beConstructedWith($tenantRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Component\MultiTenancy\Provider\TenantProvider');
    }

    public function it_implements_tenant_provider_interface()
    {
        $this->shouldImplement('SWP\Component\MultiTenancy\Provider\TenantProviderInterface');
    }

    public function it_provides_an_array_of_all_available_tenants($tenantRepository)
    {
        $tenants = [
            0 => [
                'id'        => 1,
                'name'      => 'test name',
                'subdomain' => 'example1',
            ],
            1 => [
                'id'        => 2,
                'name'      => 'test name 2',
                'subdomain' => 'example2',
            ],
        ];

        $tenantRepository->findAvailableTenants()
            ->shouldBeCalled()
            ->willReturn($tenants);

        $this->getAvailableTenants()->shouldReturn($tenants);
    }
}
