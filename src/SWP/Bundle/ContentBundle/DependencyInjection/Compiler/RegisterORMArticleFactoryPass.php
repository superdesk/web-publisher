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

class RegisterORMArticleFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swp.factory.article') ||
            !$container->hasParameter('swp_content.backend_type_orm')) {
            return;
        }

        $baseDefinition = new Definition(
            Factory::class,
            [
                new Parameter('swp.model.article.class'),
            ]
        );

        $articleFactoryDefinition = new Definition(
            $container->getParameter('swp.factory.article.class'),
            [
                $baseDefinition,
                $container->findDefinition('swp.hydrator.article'),
            ]
        );

        $container->setDefinition('swp.factory.article', $articleFactoryDefinition);
    }
}
