<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\RegisterTenantFactoryCompilerPass;
use SWP\Component\MultiTenancy\Factory\TenantFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @mixin RegisterTenantFactoryCompilerPass
 */
class RegisterTenantFactoryCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RegisterTenantFactoryCompilerPass::class);
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_creates_default_definition_of_tenant_factory(
        ContainerBuilder $container
    ) {
        $container->hasParameter('swp_multi_tenancy.factory.tenant.class')->willReturn(true);
        $container->getParameter('swp_multi_tenancy.factory.tenant.class')->willReturn(TenantFactory::class);
        $tenantFactoryDefinition = new Definition(
            TenantFactory::class,
            [
                new Parameter('swp_multi_tenancy.tenant.class'),
            ]
        );

        $container->setDefinition('swp_multi_tenancy.factory.tenant', $tenantFactoryDefinition)->shouldBeCalled();

        $this->process($container);
    }

    function it_does_not_create_default_definition_of_tenant_factory_if_tenant_factory_is_not_set(
        ContainerBuilder $container
    ) {
        $container->hasParameter('swp_multi_tenancy.factory.tenant.class')->willReturn(false);
        $container->getParameter('swp_multi_tenancy.factory.tenant.class')->shouldNotBeCalled();
        $tenantFactoryDefinition = new Definition(
            TenantFactory::class,
            [
                new Parameter('swp_multi_tenancy.tenant.class'),
            ]
        );

        $container->setDefinition('swp_multi_tenancy.factory.tenant', $tenantFactoryDefinition)->shouldNotBeCalled();
        $this->process($container);
    }
}
