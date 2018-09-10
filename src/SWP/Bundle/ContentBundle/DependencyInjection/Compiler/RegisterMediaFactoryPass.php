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

use SWP\Bundle\ContentBundle\File\FileExtensionChecker;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

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
                $container->getDefinition('swp.repository.image'),
                $container->getDefinition('swp.repository.file'),
                $baseDefinition,
                $container->getDefinition(FileExtensionChecker::class),
            ]
        );
        $mediaFactoryDefinition->setPublic(true);

        $container->setDefinition('swp.factory.media', $mediaFactoryDefinition);
    }
}
