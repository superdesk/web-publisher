<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\DependencyInjection\Compiler;

use SWP\Bundle\CoreBundle\Service\ArticlePreviewer;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

final class OverrideImageFactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('swp.factory.image')) {
            return;
        }

        $baseDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.image.class'),
            ]
        );

        $imageFactoryDefinition = new Definition(
            $container->getParameter('swp.factory.image.class'),
            [
                $container->getDefinition('swp.repository.image'),
                $baseDefinition,
                $container->getDefinition(ArticlePreviewer::class),
            ]
        );

        $imageFactoryDefinition->setPublic(true);

        $container->setDefinition('swp.factory.image', $imageFactoryDefinition);
    }
}
