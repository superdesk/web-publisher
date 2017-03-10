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
use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\RegisterTenantFactoryCompilerPass;
use SWP\Component\MultiTenancy\Factory\TenantFactory;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @mixin RegisterTenantFactoryCompilerPass
 */
class RegisterTenantFactoryCompilerPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RegisterTenantFactoryCompilerPass::class);
    }

    public function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_creates_default_definition_of_tenant_factory(
        ContainerBuilder $container
    ) {
        $container->hasDefinition('swp.factory.tenant')->willReturn(true);
        $container->getParameter('swp.factory.tenant.class')->willReturn(TenantFactory::class);

        $factoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.tenant.class'),
            ]
        );

        $tenantFactoryDefinition = new Definition(
            TenantFactory::class,
            [
                $factoryDefinition,
                new Reference('swp_multi_tenancy.random_string_generator'),
                new Reference('swp.repository.organization'),
            ]
        );

        $container->setDefinition('swp.factory.tenant', $tenantFactoryDefinition)->shouldBeCalled();

        $this->process($container);
    }

    public function it_should_not_set_tenant_factory_if_class_is_not_set(
        ContainerBuilder $container
    ) {
        $container->hasDefinition('swp.factory.tenant')->willReturn(false);
        $container->getParameter('swp.factory.tenant.class')->shouldNotBeCalled();

        $factoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.tenant.class'),
            ]
        );

        $tenantFactoryDefinition = new Definition(
            TenantFactory::class,
            [
                $factoryDefinition,
                new Reference('swp_multi_tenancy.random_string_generator'),
                new Reference('swp.repository.organization'),
            ]
        );

        $container->setDefinition('swp.factory.tenant', $tenantFactoryDefinition)->shouldNotBeCalled();

        $this->process($container);
    }
}
