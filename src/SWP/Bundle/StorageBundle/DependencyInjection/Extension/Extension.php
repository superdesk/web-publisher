<?php

/*
 * This file is part of the Superdesk Web Publisher Storage Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\StorageBundle\DependencyInjection\Extension;

use SWP\Bundle\StorageBundle\DependencyInjection\Factory\DriverFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;

abstract class Extension extends BaseExtension
{
    /**
     * Registers the storage resources (repositories etc.).
     *
     * @param $type
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function registerStorage($type, array $config, ContainerBuilder $container)
    {
        $driver = DriverFactory::createDriver($type);

        // enable current backend type for mapping files
        $container->setParameter(sprintf('%s.backend_type_%s', $this->getAlias(), $type), true);
        $resources = $container->hasParameter('swp.resources') ? $container->getParameter('swp.resources') : [];
        foreach ($config as $key => $classConfig) {
            $container->setParameter(
                sprintf('%s.persistence.%s.manager_name', $this->getAlias(), $type),
                $classConfig['object_manager_name']
            );

            $resources = array_merge($resources, ['swp.'.$key => $classConfig]);

            $classConfig['name'] = $key;
            $driver->load($container, $classConfig);
        }
        $container->setParameter('swp.resources', $resources);
    }
}
