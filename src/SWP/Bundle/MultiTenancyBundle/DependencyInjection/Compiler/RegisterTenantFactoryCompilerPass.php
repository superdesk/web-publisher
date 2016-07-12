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
namespace SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

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
        if (!$container->hasParameter('swp_multi_tenancy.factory.tenant.class')) {
            return;
        }

        $tenantFactoryDefinition = new Definition(
            $container->getParameter('swp_multi_tenancy.factory.tenant.class'),
            [
                new Parameter('swp_multi_tenancy.tenant.class'),
            ]
        );

        $container->setDefinition('swp_multi_tenancy.factory.tenant', $tenantFactoryDefinition);
    }
}
