<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\BridgeBundle\DependencyInjection;

use SWP\Component\Bridge\Model\Package;
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
        $rootNode = $treeBuilder->root('swp_bridge', 'array');

        $rootNode
            ->children()
                ->arrayNode('persistence')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('package')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Package::class)->end()
                                                ->scalarNode('repository')->defaultValue(null)->end()
                                                ->scalarNode('factory')->defaultValue(null)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end() // orm
                    ->end()
                ->end()
                ->arrayNode('api')
                    ->children()
                        ->scalarNode('host')
                            ->info('Hostname of the Content API.')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->integerNode('port')
                            ->info('Port of the Content API.')
                            ->defaultValue(80)
                        ->end()
                        ->enumNode('protocol')
                            ->info('Protocol which will be used for connection to the Content Api.')
                            ->values(['http', 'https'])
                            ->defaultValue('https')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('auth')
                    ->children()
                        ->scalarNode('client_id')
                            ->info('Client ID for OAuth2 authentication for the Content Api.')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('username')
                            ->info('Username for OAuth2 authentication for the Content Api.')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('password')
                            ->info('Password for OAuth2 authentication for the Content Api.')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->variableNode('options')->end()
            ->end();

        return $treeBuilder;
    }
}
