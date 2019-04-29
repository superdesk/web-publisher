<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Seo Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SeoBundle\DependencyInjection;

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Seo\Model\SeoImage;
use SWP\Component\Seo\Model\SeoImageInterface;
use SWP\Component\Seo\Model\SeoMetadata;
use SWP\Component\Seo\Model\SeoMetadataInterface;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): NodeParentInterface
    {
        $treeBuilder = new TreeBuilder('swp_seo');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('upload_destination')->cannotBeEmpty()->end()
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
                                    ->arrayNode('seo_metadata')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->cannotBeEmpty()->defaultValue(SeoMetadata::class)->end()
                                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('interface')->defaultValue(SeoMetadataInterface::class)->end()
                                            ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('seo_image')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->cannotBeEmpty()->defaultValue(SeoImage::class)->end()
                                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('interface')->defaultValue(SeoImageInterface::class)->end()
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
