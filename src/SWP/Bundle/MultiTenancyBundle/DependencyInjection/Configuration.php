<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('swp_multi_tenancy')
            ->children()
                ->arrayNode('persistence')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('phpcr')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->scalarNode('basepath')->defaultValue('/swp')->end()
                                ->arrayNode('route_basepaths')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(['routes'])
                                    ->info('Route base paths names')
                                ->end()
                                ->scalarNode('content_basepath')
                                    ->defaultValue('content')
                                    ->info('Content base path name')
                                ->end()
                                ->scalarNode('site_document_class')
                                    ->defaultValue('SWP\Bundle\MultiTenancyBundle\Document\Site')
                                    ->info('Site document class, represents current site/tenant in PHPCR tree')
                                ->end()
                                ->scalarNode('tenant_aware_router_class')
                                    ->defaultValue('SWP\Bundle\MultiTenancyBundle\Routing\TenantAwareRouter')
                                    ->info('Tenant aware router FQCN')
                                ->end()
                                ->scalarNode('document_class')
                                    ->defaultValue('SWP\Bundle\MultiTenancyBundle\Document\Page')
                                    ->info('The class for the pages used by PHPCR initializer')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
