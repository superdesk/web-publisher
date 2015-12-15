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
namespace spec\SWP\MultiTenancyBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\MultiTenancyBundle\Context\TenantContextInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SWP\MultiTenancyBundle\Model\TenantInterface;
use SWP\MultiTenancyBundle\Model\TenantAwareInterface;

class TenantSubscriberSpec extends ObjectBehavior
{
    public function let(TenantContextInterface $tenantContext)
    {
        $this->beConstructedWith($tenantContext);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\MultiTenancyBundle\EventListener\TenantSubscriber');
    }

    public function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    public function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn(array(Events::prePersist));
    }

    public function it_set_the_tenant_on_pre_persist_doctrine_event(
        $tenantContext,
        LifecycleEventArgs $event,
        TenantAwareInterface $tenantAware,
        TenantInterface $tenant
    ) {
        $event->getEntity()->willReturn($tenantAware);
        $tenant->getId()->willReturn(1);
        $tenantContext->getTenant()->shouldBeCalled()->willReturn($tenant);

        $tenantAware->setTenant($tenant)->shouldBeCalled();

        $this->prePersist($event)->shouldBeNull();
    }

    public function it_sets_only_tenant_aware_interface_implementation_on_pre_presist(
        TenantAwareInterface $tenantAware,
        LifecycleEventArgs $event
    ) {
        $item = new \stdClass();
        $event->getEntity()->willReturn($item);
        $tenantAware->setTenant(Argument::any())->shouldNotBeCalled();

        $this->prePersist($event)->shouldBeNull();
    }
}
