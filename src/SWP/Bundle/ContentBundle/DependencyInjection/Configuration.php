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

namespace SWP\Bundle\ContentBundle\DependencyInjection;

use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\ArticleRepository;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Route;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Site;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Media;
use SWP\Bundle\ContentBundle\Doctrine\ORM\Article as ORMArticle;
use SWP\Bundle\ContentBundle\Doctrine\ORM\ArticleRepository as ORMArticleRepository;
use SWP\Bundle\ContentBundle\Doctrine\ORM\Route as ORMRoute;
use SWP\Bundle\ContentBundle\Factory\ArticleFactory;
use SWP\Bundle\ContentBundle\Factory\RouteFactory;
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
                                ->scalarNode('default_content_path')
                                    ->defaultValue('articles')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('article')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Article::class)->end()
                                                ->scalarNode('repository')->defaultValue(ArticleRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(ArticleFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('site')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Site::class)->end()
                                                ->scalarNode('repository')->defaultValue(null)->end()
                                                ->scalarNode('factory')->defaultValue(RouteFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('route')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Route::class)->end()
                                                ->scalarNode('repository')->defaultValue(null)->end()
                                                ->scalarNode('factory')->defaultValue(RouteFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('media')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Media::class)->end()
                                                ->scalarNode('repository')->defaultValue(null)->end()
                                                ->scalarNode('factory')->defaultValue(null)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end() // classes
                            ->end()
                        ->end() // phpcr
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('article')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(ORMArticle::class)->end()
                                                ->scalarNode('repository')->defaultValue(ORMArticleRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(ArticleFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('site')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Site::class)->end()
                                                ->scalarNode('repository')->defaultValue(null)->end()
                                                ->scalarNode('factory')->defaultValue(RouteFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('route')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(ORMRoute::class)->end()
                                                ->scalarNode('repository')->defaultValue(null)->end()
                                                ->scalarNode('factory')->defaultValue(RouteFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('media')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Media::class)->end()
                                                ->scalarNode('repository')->defaultValue(null)->end()
                                                ->scalarNode('factory')->defaultValue(null)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end() // classes
                            ->end()
                        ->end() // orm
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
