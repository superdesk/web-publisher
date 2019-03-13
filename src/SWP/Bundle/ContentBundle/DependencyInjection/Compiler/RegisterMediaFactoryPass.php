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

namespace SWP\Bundle\ContentBundle\DependencyInjection\Compiler;

use SWP\Bundle\ContentBundle\Provider\ORM\ArticleMediaAssetProvider;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

class RegisterMediaFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swp.factory.media')) {
            return;
        }

        $baseDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.media.class'),
            ]
        );

        $mediaFactoryDefinition = new Definition(
            $container->getParameter('swp.factory.media.class'),
            [
                new Reference(ArticleMediaAssetProvider::class),
                $baseDefinition,
                new Reference('swp.factory.image_rendition'),
                new Reference('swp_content_bundle.manager.media'),
                new Reference('monolog.logger.swp_asset_download'),
            ]
        );
        $mediaFactoryDefinition->setPublic(true);

        $container->setDefinition('swp.factory.media', $mediaFactoryDefinition);
    }
}
