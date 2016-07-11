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
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Configures Symfony CMF PrefixCandidates to make the prefixes tenant aware.
 */
class ConfigurePrefixCandidatesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('cmf_routing.backend_type_phpcr')
            || !$container->hasParameter('swp_multi_tenancy.backend_type_phpcr')) {
            return;
        }

        $bundles = $container->getParameter('kernel.bundles');
        if (!isset($bundles['CmfRoutingBundle'])) {
            throw new RuntimeException(
                'You have enabled the PHPCR backend but the CmfRoutingBundle is not registered'
            );
        }

        $container->getDefinition('cmf_routing.phpcr_candidates_prefix')
            ->setClass($container->getParameter('swp_multi_tenancy.prefix_candidates.class'))
            ->addMethodCall('setPathBuilder', [
                new Reference('swp_multi_tenancy.path_builder'),
            ])
            ->addMethodCall('setRoutePathsNames', [
                $container->getParameter('swp_multi_tenancy.persistence.phpcr.route_basepaths'),
            ]);
    }
}
