<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentListBundle\DependencyInjection\Compiler;

use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

class RegisterContentListFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swp.factory.content_list')) {
            return;
        }

        $baseDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.content_list.class'),
            ]
        );

        $factoryDefinition = new Definition(
            $container->getParameter('swp.factory.content_list.class'),
            [
                $baseDefinition,
            ]
        );

        $container->setDefinition('swp.factory.content_list', $factoryDefinition);
    }
}
