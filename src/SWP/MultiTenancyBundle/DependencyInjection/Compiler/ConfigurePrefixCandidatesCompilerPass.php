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
namespace SWP\MultiTenancyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Configures Symfony CMF PrefixCandidates service by adding
 * custom configurator which sets tenant aware base paths.
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

        $container->getDefinition('cmf_routing.phpcr_candidates_prefix')
            ->setConfigurator([
                new Reference('swp_multi_tenancy.candidates_configurator'),
                'configure',
            ])
        ;
    }
}
