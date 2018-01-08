<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;

class RegisterContainerWidgetFactory implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swp.factory.container_widget')) {
            return;
        }

        $containerWidgetFactoryDefinition = new Definition(
            $container->getParameter('swp.factory.container_widget.class'),
            [
                new Parameter('swp.model.container_widget.class'),
            ]
        );
        $containerWidgetFactoryDefinition->setPublic(true);

        $container->setDefinition('swp.factory.container_widget', $containerWidgetFactoryDefinition);
    }
}
