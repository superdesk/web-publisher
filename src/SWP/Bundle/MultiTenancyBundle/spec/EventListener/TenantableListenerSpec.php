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

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\Tenant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TenantableListenerSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        TenantContextInterface $tenantContext
    ) {
        $this->beConstructedWith($entityManager, $tenantContext);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\MultiTenancyBundle\EventListener\TenantableListener');
    }

    public function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    public function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn([
            KernelEvents::REQUEST => 'onKernelRequest',
        ]);
    }

    public function it_skips_tenantable_filter_on_kernel_request(
        GetResponseEvent $event,
        $tenantContext,
        $entityManager
    ) {
        $tenantContext->getTenant()->shouldBeCalled()->willReturn(new Tenant());
        $entityManager->getFilters()->shouldNotBeCalled();

        $this->onKernelRequest($event)->shouldBeNull();
    }
}
