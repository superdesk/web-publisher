<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\DependencyInjection\Compiler;

use SWP\Bundle\ContentBundle\File\FileExtensionChecker;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

final class RegisterFileFactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('swp.factory.file')) {
            return;
        }

        $baseDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.file.class'),
            ]
        );

        $fileFactoryDefinition = new Definition(
            $container->getParameter('swp.factory.file.class'),
            [
                $container->getDefinition(FileExtensionChecker::class),
                $container->getDefinition('swp.factory.image'),
                $baseDefinition,
            ]
        );

        $fileFactoryDefinition->setPublic(true);

        $container->setDefinition('swp.factory.file', $fileFactoryDefinition);
    }
}
