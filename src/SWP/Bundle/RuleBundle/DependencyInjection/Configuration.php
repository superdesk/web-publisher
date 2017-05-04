<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\RuleBundle\DependencyInjection;

use SWP\Bundle\RuleBundle\Doctrine\ORM\RuleRepository;
use SWP\Component\Rule\Model\Rule;
use SWP\Component\Rule\Model\RuleInterface;
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
        $treeBuilder->root('swp_rule')
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
                                        ->arrayNode('rule')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Rule::class)->end()
                                                ->scalarNode('repository')->defaultValue(RuleRepository::class)->end()
                                                ->scalarNode('interface')->defaultValue(RuleInterface::class)->end()
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
