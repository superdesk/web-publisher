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
namespace spec\SWP\Bundle\MultiTenancyBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\Tenant;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TenantSubscriberSpec extends ObjectBehavior
{
    public function let(ContainerInterface $container)
    {
        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\MultiTenancyBundle\EventListener\TenantSubscriber');
    }

    public function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Doctrine\Common\EventSubscriber');
    }

    public function it_subscribes_to_an_event()
    {
        $this::getSubscribedEvents()->shouldReturn([Events::prePersist]);
    }

    public function it_should_skip_when_tenant_is_set_on_tenant_aware_object(
        LifecycleEventArgs $event,
        TenantAwareInterface $tenantAware
    ) {
        $tenant = new Tenant();
        $tenant->setSubdomain('example.com');
        $tenant->setName('Example');

        $tenantAware->getTenant()->shouldBeCalled()->willReturn($tenant);
        $event->getEntity()->willReturn($tenantAware);

        $this->prePersist($event)->shouldReturn(null);
    }

    public function it_sets_the_tenant_on_pre_persist_doctrine_event(
        TenantContextInterface $tenantContext,
        LifecycleEventArgs $event,
        TenantAwareInterface $tenantAware,
        $container
    ) {
        $tenant = new Tenant();
        $tenant->setSubdomain('example.com');
        $tenant->setName('Example');

        $tenantAware->getTenant()->shouldBeCalled()->willReturn(null);
        $event->getEntity()->willReturn($tenantAware);
        $tenantContext->getTenant()->shouldBeCalled()->willReturn($tenant);
        $container->get('swp_multi_tenancy.tenant_context')->willReturn($tenantContext);

        $tenantAware->setTenant($tenant)->shouldBeCalled();

        $this->prePersist($event)->shouldBeNull();
    }

    public function it_sets_only_tenant_aware_interface_implementation_on_pre_presist(
        TenantAwareInterface $tenantAware,
        LifecycleEventArgs $event
    ) {
        $item = new \stdClass();
        $event->getEntity()->willReturn($item);
        $tenantAware->getTenant()->shouldNotBeCalled();
        $tenantAware->setTenant(Argument::any())->shouldNotBeCalled();

        $this->prePersist($event)->shouldBeNull();
    }
}
