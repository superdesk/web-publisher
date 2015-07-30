<?php

namespace SWP\UpdaterBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('swp_updater', 'array');

        $rootNode
            ->children()
                ->scalarNode('version_class')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('temp_dir')
                    ->defaultValue('default')
                ->end()
                ->scalarNode('target_dir')
                    ->defaultValue('default')
                ->end()
                ->arrayNode('client')
                    ->children()
                        ->scalarNode('base_uri')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
