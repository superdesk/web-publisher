<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

abstract class AbstractOverridePass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     * @param string           $serviceId
     * @param string           $classNamespace
     *
     * @return Definition|void
     */
    public function overrideDefinitionClassIfExists(
        ContainerBuilder $container,
        $serviceId,
        $classNamespace
    ) {
        if (null === $definition = $this->getDefinitionIfExists($container, $serviceId)) {
            return;
        }

        $definition->setClass($classNamespace);

        return $definition;
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $serviceId
     *
     * @return Definition|void
     */
    public function getDefinitionIfExists(ContainerBuilder $container, $serviceId)
    {
        if (!$container->hasDefinition($serviceId)) {
            return;
        }

        return $container->getDefinition($serviceId);
    }
}
