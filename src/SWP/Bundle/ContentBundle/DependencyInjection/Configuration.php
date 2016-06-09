<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\DependencyInjection;

use SWP\Bundle\ContentBundle\Doctrine\Phpcr\Article;
use SWP\Bundle\ContentBundle\Doctrine\Phpcr\ArticleRepository;
use SWP\Bundle\ContentBundle\Doctrine\Phpcr\Route;
use SWP\Bundle\ContentBundle\Doctrine\Phpcr\Site;
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
        $treeBuilder->root('swp_content')
            ->children()
                ->arrayNode('persistence')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('phpcr')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->scalarNode('object_manager_name')
                                    ->defaultValue('default')
                                ->end()
                                    ->scalarNode('article_manager_name')
                                    ->defaultValue('swp.manager.article.phpcr')
                                ->end()
                                ->arrayNode('repositories')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('article')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Article::class)->end()
                                                ->scalarNode('class')->defaultValue(ArticleRepository::class)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('site')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Site::class)->end()
                                                ->scalarNode('class')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('route')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Route::class)->end()
                                                ->scalarNode('class')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end() // phpcr
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->scalarNode('manager_name')
                                    ->defaultNull()
                                ->end()
                            ->end()
                        ->end() // orm
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
