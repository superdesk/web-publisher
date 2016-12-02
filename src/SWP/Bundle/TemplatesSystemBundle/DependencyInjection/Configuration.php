<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\DependencyInjection;

use SWP\Bundle\TemplatesSystemBundle\Factory\ContainerDataFactory;
use SWP\Bundle\TemplatesSystemBundle\Factory\ContainerFactory;
use SWP\Bundle\TemplatesSystemBundle\Model\Container;
use SWP\Bundle\TemplatesSystemBundle\Model\ContainerData;
use SWP\Bundle\TemplatesSystemBundle\Model\ContainerWidget;
use SWP\Bundle\TemplatesSystemBundle\Model\WidgetModel;
use SWP\Bundle\TemplatesSystemBundle\Repository\ContainerRepository;
use SWP\Bundle\TemplatesSystemBundle\Repository\WidgetModelRepository;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerDataInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerWidgetInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
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
        $treeBuilder->root('swp_templates_system')
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
                                        ->arrayNode('container')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Container::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ContainerInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(ContainerRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(ContainerFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('container_data')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(ContainerData::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ContainerDataInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(null)->end()
                                                ->scalarNode('factory')->defaultValue(ContainerDataFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('widget_model')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(WidgetModel::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(WidgetModelInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(WidgetModelRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(null)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('container_widget')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(ContainerWidget::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ContainerWidgetInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(null)->end()
                                                ->scalarNode('factory')->defaultValue(null)->end()
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
