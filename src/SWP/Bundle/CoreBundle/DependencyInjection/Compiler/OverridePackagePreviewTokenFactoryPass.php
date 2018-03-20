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

use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

class OverridePackagePreviewTokenFactoryPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swp.factory.package_preview_token')) {
            return;
        }

        $baseDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.package_preview_token.class'),
            ]
        );

        $factoryDefinition = new Definition(
            $container->getParameter('swp.factory.package_preview_token.class'),
            [
                $baseDefinition,
                new Reference('swp.random_string_generator'),
            ]
        );

        $factoryDefinition->setPublic(true);

        $container->setDefinition('swp.factory.package_preview_token', $factoryDefinition);
    }
}
