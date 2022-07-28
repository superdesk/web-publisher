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

use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

class RegisterRouteFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swp.factory.route')) {
            return;
        }

        $baseDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.route.class'),
            ]
        );

        $routeFactoryDefinition = new Definition(
            $container->getParameter('swp.factory.route.class'),
            [
                $baseDefinition,
            ]
        );
        $routeFactoryDefinition->setPublic(true);

        $container->setDefinition('swp.factory.route', $routeFactoryDefinition);
    }
}
