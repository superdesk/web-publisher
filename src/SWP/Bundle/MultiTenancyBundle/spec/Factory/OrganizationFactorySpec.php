<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\MultiTenancyBundle\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\HierarchyInterface;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\MultiTenancyBundle\Factory\OrganizationFactory;
use SWP\Bundle\MultiTenancyBundle\spec\ParentTest;
use SWP\Component\MultiTenancy\Factory\OrganizationFactoryInterface;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;

/**
 * @mixin OrganizationFactory
 */
class OrganizationFactorySpec extends ObjectBehavior
{
    public function let(
        OrganizationFactoryInterface $factory,
        ObjectManager $documentManager
    ) {
        $this->beConstructedWith($factory, $documentManager, '/swp');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(OrganizationFactory::class);
    }

    public function it_implements_tenant_factory_interface()
    {
        $this->shouldImplement(OrganizationFactoryInterface::class);
    }

    public function it_creates_empty_organization(
        OrganizationFactoryInterface $factory,
        OrganizationInterface $organization
    ) {
        $factory->create()->willReturn($organization);

        $this->create()->shouldReturn($organization);
    }

    public function it_creates_a_new_organization_with_code(
        OrganizationFactoryInterface $factory,
        OrganizationInterface $organization
    ) {
        $factory->createWithCode()->willReturn($organization);

        $this->createWithCode()->shouldReturn($organization);
    }

    public function it_creates_a_new_organization_with_code_and_parent_document(
        OrganizationFactoryInterface $factory,
        ObjectManager $documentManager
    ) {
        $organization = new ParentTest();

        $factory->createWithCode()->willReturn($organization);

        $documentManager->find(null, '/swp')->shouldBeCalled();

        $this->createWithCode()->shouldHaveType(HierarchyInterface::class);
    }
}
