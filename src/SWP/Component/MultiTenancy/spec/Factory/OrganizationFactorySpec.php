<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Component\MultiTenancy\Factory;

use PhpSpec\ObjectBehavior;
use SWP\Component\Common\Generator\GeneratorInterface;
use SWP\Component\MultiTenancy\Factory\OrganizationFactory;
use SWP\Component\MultiTenancy\Factory\OrganizationFactoryInterface;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * @mixin OrganizationFactory
 */
class OrganizationFactorySpec extends ObjectBehavior
{
    function let(
        FactoryInterface $factory,
        GeneratorInterface $generator
    ) {
        $this->beConstructedWith($factory, $generator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrganizationFactory::class);
    }

    function it_implements_organization_factory_interface()
    {
        $this->shouldImplement(OrganizationFactoryInterface::class);
    }

    function it_creates_empty_organization(FactoryInterface $factory, OrganizationInterface $organization)
    {
        $factory->create()->willReturn($organization);
        $this->create()->shouldReturn($organization);
    }

    function it_creates_a_new_organization_with_code(
        FactoryInterface $factory,
        GeneratorInterface $generator,
        OrganizationInterface $organization
    ) {
        $factory->create()->willReturn($organization);
        $generator->generate(6)->willReturn('123456');
        $organization->setCode('123456')->shouldBeCalled();

        $this->createWithCode()->shouldReturn($organization);
    }
}
