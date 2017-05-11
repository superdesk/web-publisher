<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\DependencyInjection;

use SWP\Bundle\SettingsBundle\Model\SettingsInterface;
use SWP\Bundle\SettingsBundle\Model\SettingsRepository;
use SWP\Bundle\SettingsBundle\Model\Settings;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

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
        $treeBuilder->root('swp_settings')
            ->children()
                ->scalarNode('cache_service')->defaultNull()->info('A service implementing Psr\Cache\CacheItemPoolInterface')->end()
                ->integerNode('cache_lifetime')->defaultValue(3600)->end()
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
                                    ->arrayNode('settings')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->cannotBeEmpty()->defaultValue(Settings::class)->end()
                                            ->scalarNode('repository')->defaultValue(SettingsRepository::class)->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('interface')->defaultValue(SettingsInterface::class)->end()
                                            ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                        ->end()
                                    ->end()
                                ->end() // classes
                                ->end() // array node
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('settings')
                    ->useAttributeAsKey('settings')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('scope')->defaultValue('global')->end()
                            ->scalarNode('value')
                                ->beforeNormalization()
                                ->ifArray()
                                ->then(function ($value) {
                                    return json_encode($value);
                                })
                                ->end()
                                ->defaultValue(null)
                            ->end()
                            ->scalarNode('type')
                                ->defaultValue('string')
                                ->validate()
                                    ->always(function ($v) {
                                        if (!in_array($v, ['string', 'array', 'boolean'])) {
                                            throw new InvalidTypeException();
                                        }

                                        return $v;
                                    })
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
