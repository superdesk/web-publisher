<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Redirect Route Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\RedirectRouteBundle\DependencyInjection;

use SWP\Bundle\RedirectRouteBundle\Model\RedirectRoute;
use SWP\Bundle\RedirectRouteBundle\Model\RedirectRouteInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('swp_redirect_route');
        $treeBuilder->getRootNode()
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
                                    ->arrayNode('redirect_route')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->cannotBeEmpty()->defaultValue(RedirectRoute::class)->end()
                                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('interface')->defaultValue(RedirectRouteInterface::class)->end()
                                            ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                        ->end()
                                    ->end()
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
