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

use SWP\Bundle\ContentBundle\Doctrine\ORM\ArticleAuthorRepository;
use SWP\Bundle\ContentBundle\Doctrine\ORM\ArticleRepository;
use SWP\Bundle\ContentBundle\Doctrine\ORM\FileRepository;
use SWP\Bundle\ContentBundle\Doctrine\ORM\RelatedArticleRepository;
use SWP\Bundle\ContentBundle\Doctrine\ORM\RouteRepository;
use SWP\Bundle\ContentBundle\Doctrine\ORM\ArticleMediaRepository;
use SWP\Bundle\ContentBundle\Doctrine\ORM\ImageRepository;
use SWP\Bundle\ContentBundle\Doctrine\ORM\SlideshowItemRepository;
use SWP\Bundle\ContentBundle\Doctrine\ORM\SlideshowRepository;
use SWP\Bundle\ContentBundle\Factory\FileFactory;
use SWP\Bundle\ContentBundle\Factory\KeywordFactory;
use SWP\Bundle\ContentBundle\Factory\ORM\ArticleFactory;
use SWP\Bundle\ContentBundle\Factory\ORM\ImageRenditionFactory;
use SWP\Bundle\ContentBundle\Factory\ORM\MediaFactory;
use SWP\Bundle\ContentBundle\Factory\RouteFactory;
use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\ArticlePreviousRelativeUrl;
use SWP\Bundle\ContentBundle\Model\ArticlePreviousRelativeUrlInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSource;
use SWP\Bundle\ContentBundle\Model\ArticleSourceInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSourceReference;
use SWP\Bundle\ContentBundle\Model\ArticleSourceReferenceInterface;
use SWP\Bundle\ContentBundle\Model\ArticleAuthor;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use SWP\Bundle\ContentBundle\Model\AuthorMedia;
use SWP\Bundle\ContentBundle\Model\AuthorMediaInterface;
use SWP\Bundle\ContentBundle\Model\File;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\Image;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Bundle\ContentBundle\Model\Keyword;
use SWP\Bundle\ContentBundle\Model\KeywordInterface;
use SWP\Bundle\ContentBundle\Model\Metadata;
use SWP\Bundle\ContentBundle\Model\MetadataInterface;
use SWP\Bundle\ContentBundle\Model\RelatedArticle;
use SWP\Bundle\ContentBundle\Model\RelatedArticleInterface;
use SWP\Bundle\ContentBundle\Model\Route;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\Service;
use SWP\Bundle\ContentBundle\Model\ServiceInterface;
use SWP\Bundle\ContentBundle\Model\Slideshow;
use SWP\Bundle\ContentBundle\Model\SlideshowInterface;
use SWP\Bundle\ContentBundle\Model\SlideshowItem;
use SWP\Bundle\ContentBundle\Model\SlideshowItemInterface;
use SWP\Bundle\ContentBundle\Model\Subject;
use SWP\Bundle\ContentBundle\Model\SubjectInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
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
    private const ADAPTERS = ['aws_adapter', 'local_adapter'];

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('swp_content');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('media_storage_adapter')
                    ->defaultValue('local_adapter')
                    ->info('Choose media storage adapter from the following list: "aws_adapter", "local_adapter"')
                    ->validate()
                        ->ifNotInArray(self::ADAPTERS)
                        ->thenInvalid('Invalid media adapter %s.')
                    ->end()
                ->end()
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
                                        ->arrayNode('article')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Article::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ArticleInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(ArticleRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(ArticleFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('related_article')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(RelatedArticle::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(RelatedArticleInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(RelatedArticleRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('author')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(ArticleAuthor::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ArticleAuthorInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(ArticleAuthorRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('article_source')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(ArticleSource::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ArticleSourceInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('article_source_reference')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(ArticleSourceReference::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ArticleSourceReferenceInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('route')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Route::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(RouteInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(RouteRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(RouteFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('media')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(ArticleMedia::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ArticleMediaInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(ArticleMediaRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(MediaFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('author_media')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(AuthorMedia::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(AuthorMediaInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('image')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Image::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ImageInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(ImageRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('file')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(File::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(FileInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(FileRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(FileFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('slideshow')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Slideshow::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(SlideshowInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(SlideshowRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('slideshow_item')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(SlideshowItem::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(SlideshowItemInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(SlideshowItemRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('image_rendition')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(ImageRendition::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ImageRenditionInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(ImageRenditionFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('keyword')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Keyword::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(KeywordInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(KeywordFactory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('previous_relative_url')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(ArticlePreviousRelativeUrl::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ArticlePreviousRelativeUrlInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('metadata')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Metadata::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(MetadataInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('subject')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Subject::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(SubjectInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('service')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Service::class)->end()
                                                ->scalarNode('interface')->cannotBeEmpty()->defaultValue(ServiceInterface::class)->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
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
