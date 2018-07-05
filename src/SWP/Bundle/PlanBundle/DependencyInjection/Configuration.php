<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Plan Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\PlanBundle\DependencyInjection;

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Plan\Model\Plan;
use SWP\Component\Plan\Model\PlanInterface;
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
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('swp_plan')
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
                                        ->arrayNode('plan')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Plan::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('interface')->defaultValue(PlanInterface::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
