<?php

namespace spec\SWP\MultiTenancyBundle\Provider;

use PhpSpec\ObjectBehavior;
use SWP\MultiTenancyBundle\Repository\TenantRepositoryInterface;
use SWP\MultiTenancyBundle\Model\TenantInterface;

class TenantProviderSpec extends ObjectBehavior
{
    public function let(TenantRepositoryInterface $tenantRepository)
    {
        $this->beConstructedWith($tenantRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\MultiTenancyBundle\Provider\TenantProvider');
    }

    public function it_implements_tenant_provider_interface()
    {
        $this->shouldImplement('SWP\MultiTenancyBundle\Provider\TenantProviderInterface');
    }

    public function it_provides_an_array_of_all_available_tenants($tenantRepository, TenantInterface $tenant)
    {
        $tenants = array(
            0 => array(
                'id' => 1,
                'name' => 'test name',
                'subdomain' => 'example1',
            ),
            1 => array(
                'id' => 2,
                'name' => 'test name 2',
                'subdomain' => 'example2',
            ),
        );

        $tenantRepository->findAvailableTenants()
            ->shouldBeCalled()
            ->willReturn($tenants);

        $this->getAvailableTenants()->shouldReturn($tenants);
    }
}
