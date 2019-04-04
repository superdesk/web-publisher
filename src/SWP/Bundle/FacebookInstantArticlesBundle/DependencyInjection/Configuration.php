<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Facebook Instant Articles Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FacebookInstantArticlesBundle\DependencyInjection;

use SWP\Bundle\FacebookInstantArticlesBundle\Model\Application;
use SWP\Bundle\FacebookInstantArticlesBundle\Model\ApplicationInterface;
use SWP\Bundle\FacebookInstantArticlesBundle\Model\Page;
use SWP\Bundle\FacebookInstantArticlesBundle\Model\PageInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('swp_facebook_instant_articles');
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
                                        ->arrayNode('facebook_application')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Application::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ApplicationInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('facebook_page')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Page::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(PageInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
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
