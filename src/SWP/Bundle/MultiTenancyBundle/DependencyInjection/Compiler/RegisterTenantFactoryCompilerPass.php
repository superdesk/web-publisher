<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler;

use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterTenantFactoryCompilerPass.
 */
class RegisterTenantFactoryCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swp.factory.tenant')) {
            return;
        }

        $factoryDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.tenant.class'),
            ]
        );

        $tenantFactoryDefinition = new Definition(
            $container->getParameter('swp.factory.tenant.class'),
            [
                $factoryDefinition,
                new Reference('swp_multi_tenancy.random_string_generator'),
                new Reference('swp.repository.organization'),
            ]
        );

        $container->setDefinition('swp.factory.tenant', $tenantFactoryDefinition);
    }
}
