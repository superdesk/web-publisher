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

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Bridge\Model\Event;
use SWP\Component\Bridge\Model\Event\Date;
use SWP\Component\Bridge\Model\Event\DateInterface;
use SWP\Component\Bridge\Model\Event\Location;
use SWP\Component\Bridge\Model\Event\LocationInterface;
use SWP\Component\Bridge\Model\EventInterface;
use SWP\Component\Bridge\Model\ExternalData;
use SWP\Component\Bridge\Model\ExternalDataInterface;
use SWP\Component\Bridge\Model\Group;
use SWP\Component\Bridge\Model\GroupInterface;
use SWP\Component\Bridge\Model\Item;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\Package;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Bridge\Model\Rendition;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Component\Storage\Factory\Factory;
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
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('interface')->defaultValue(PackageInterface::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('item')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Item::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('interface')->defaultValue(ItemInterface::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('rendition')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Rendition::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('interface')->defaultValue(RenditionInterface::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('external_data')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(ExternalData::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('interface')->defaultValue(ExternalDataInterface::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('group')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Group::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('interface')->defaultValue(GroupInterface::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('event')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Event::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('interface')->defaultValue(EventInterface::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('event_date')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Date::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('interface')->defaultValue(DateInterface::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('event_location')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Location::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('interface')->defaultValue(LocationInterface::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('event_occur_status')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Event\OccurStatus::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('interface')->defaultValue(Event\OccurStatusInterface::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('event_category')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Event\Category::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('interface')->defaultValue(Event\CategoryInterface::class)->end()
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
