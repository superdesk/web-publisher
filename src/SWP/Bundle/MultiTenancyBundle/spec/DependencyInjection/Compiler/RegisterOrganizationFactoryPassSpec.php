<?php

/*
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

namespace spec\SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\RegisterOrganizationFactoryPass;
use SWP\Bundle\MultiTenancyBundle\Factory\OrganizationFactory as BundledFactory;
use SWP\Component\MultiTenancy\Factory\OrganizationFactory;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @mixin RegisterOrganizationFactoryPass
 */
class RegisterOrganizationFactoryPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RegisterOrganizationFactoryPass::class);
    }

    public function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_creates_default_definition_of_organization_factory(
        ContainerBuilder $container
    ) {
        $container->hasDefinition('swp.factory.organization')->willReturn(true);
        $container->getParameter('swp.factory.organization.class')->willReturn(BundledFactory::class);
        $container->hasParameter('swp_multi_tenancy.persistence.phpcr.basepath')->willReturn(true);

        $factoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.organization.class'),
            ]
        );

        $organizationFactoryDefinition = new Definition(
            OrganizationFactory::class,
            [
                $factoryDefinition,
                new Reference('swp_multi_tenancy.random_string_generator'),
            ]
        );

        $organizationFactoryDefinition = new Definition(
            BundledFactory::class,
            [
                $organizationFactoryDefinition,
                new Reference('swp.object_manager.organization'),
                new Parameter('swp_multi_tenancy.persistence.phpcr.basepath'),
            ]
        );
        $organizationFactoryDefinition->setPublic(true);

        $container->setDefinition('swp.factory.organization', $organizationFactoryDefinition)->shouldBeCalled();

        $this->process($container);
    }

    public function it_should_not_set_organization_factory_if_class_is_not_set(
        ContainerBuilder $container
    ) {
        $container->hasDefinition('swp.factory.organization')->willReturn(false);
        $container->getParameter('swp.factory.organization.class')->shouldNotBeCalled();
        $container->hasParameter('swp_multi_tenancy.persistence.phpcr.basepath')->shouldNotBeCalled();

        $factoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.organization.class'),
            ]
        );

        $organizationFactoryDefinition = new Definition(
            OrganizationFactory::class,
            [
                $factoryDefinition,
                new Reference('swp_multi_tenancy.random_string_generator'),
            ]
        );

        $organizationFactoryDefinition = new Definition(
            BundledFactory::class,
            [
                $organizationFactoryDefinition,
                new Reference('swp.object_manager.organization'),
                new Parameter('swp_multi_tenancy.persistence.phpcr.basepath'),
            ]
        );

        $container->setDefinition('swp.factory.organization', $organizationFactoryDefinition)->shouldNotBeCalled();

        $this->process($container);
    }
}
