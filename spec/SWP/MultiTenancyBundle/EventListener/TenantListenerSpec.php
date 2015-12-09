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
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\ORM\EntityManagerInterface;
use SWP\MultiTenancyBundle\Context\TenantContextInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class TenantListenerSpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $entityManager, TenantContextInterface $tenantContext)
    {
        $this->beConstructedWith($entityManager, $tenantContext);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\MultiTenancyBundle\EventListener\TenantListener');
    }

    public function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    public function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn(array(
            KernelEvents::REQUEST => 'onKernelRequest',
        ));
    }

    public function it_skips_tenantable_filter_on_kernel_request(
        GetResponseEvent $event,
        $tenantContext,
        $entityManager
    ) {
        $fakeTenant = new \stdClass();
        $tenantContext->getTenant()->shouldBeCalled()->willReturn($fakeTenant);
        $entityManager->getFilters()->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }
}
