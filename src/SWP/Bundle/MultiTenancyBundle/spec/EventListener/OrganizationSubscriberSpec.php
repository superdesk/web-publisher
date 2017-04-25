<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\MultiTenancyBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\MultiTenancyBundle\EventListener\OrganizationSubscriber;
use SWP\Component\Common\Exception\UnexpectedTypeException;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\Organization;
use SWP\Component\MultiTenancy\Model\OrganizationAwareInterface;
use SWP\Component\MultiTenancy\Model\Tenant;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class OrganizationSubscriberSpec extends ObjectBehavior
{
    public function let(ContainerInterface $container)
    {
        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(OrganizationSubscriber::class);
    }

    public function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    public function it_subscribes_to_an_event()
    {
        $this->getSubscribedEvents()->shouldReturn([Events::prePersist, Events::preUpdate]);
    }

    public function it_should_skip_when_organization_is_already_set_on_organization_aware_object(
        LifecycleEventArgs $event,
        OrganizationAwareInterface $organizationAware
    ) {
        $organization = new Organization();
        $organization->setName('org1');
        $organization->setEnabled(true);
        $organization->setCode('123456');

        $organizationAware->getOrganization()->shouldBeCalled()->willReturn($organization);
        $event->getEntity()->willReturn($organizationAware);

        $this->prePersist($event);
        $this->preUpdate($event);
    }

    public function it_sets_the_organization_on_pre_persist_doctrine_event(
        TenantContextInterface $tenantContext,
        LifecycleEventArgs $event,
        OrganizationAwareInterface $organizationAware,
        ContainerInterface $container,
        ObjectManager $objectManager
    ) {
        $organization = new Organization();
        $organization->setName('org1');
        $organization->setEnabled(true);
        $organization->setCode('123456');

        $tenant = new Tenant();
        $tenant->setSubdomain('example.com');
        $tenant->setName('Example');
        $tenant->setCode('avc2334');
        $tenant->setOrganization($organization);

        $organizationAware->getOrganization()->shouldBeCalled()->willReturn(null);
        $event->getEntity()->willReturn($organizationAware);
        $objectManager->merge($organization)->willReturn($organization);
        $event->getObjectManager()->willReturn($objectManager);
        $tenantContext->getTenant()->shouldBeCalled()->willReturn($tenant);

        $organizationAware->setOrganization($organization)->shouldBeCalled();

        $container->get('swp_multi_tenancy.tenant_context')->willReturn($tenantContext);

        $this->prePersist($event);
        $this->preUpdate($event);
    }

    public function it_throws_exception_when_no_organization_on_pre_persist_doctrine_event(
        TenantContextInterface $tenantContext,
        LifecycleEventArgs $event,
        OrganizationAwareInterface $organizationAware,
        ContainerInterface $container
    ) {
        $tenant = new Tenant();
        $tenant->setDomainName('example.com');
        $tenant->setName('Example');
        $tenant->setCode('avc2334');

        $organizationAware->getOrganization()->shouldBeCalled()->willReturn(null);
        $event->getEntity()->willReturn($organizationAware);
        $tenantContext->getTenant()->shouldBeCalled()->willReturn($tenant);
        $container->get('swp_multi_tenancy.tenant_context')->willReturn($tenantContext);

        $this->shouldThrow(UnexpectedTypeException::class)
            ->duringAddOrganization($event);
    }

    public function it_sets_only_organization_aware_interface_implementation_on_pre_presist(
        OrganizationAwareInterface $organizationAware,
        LifecycleEventArgs $event
    ) {
        $item = new \stdClass();
        $event->getEntity()->willReturn($item);
        $organizationAware->getOrganization()->shouldNotBeCalled();

        $this->prePersist($event);
        $this->preUpdate($event);
    }
}
