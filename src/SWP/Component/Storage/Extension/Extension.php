<?php

/**
 * This file is part of the Superdesk Web Publisher Storage Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\Storage\Extension;

use SWP\Component\Storage\DependencyInjection\Factory\DriverFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;

abstract class Extension extends BaseExtension
{
    public function registerStorage($type, array $config, ContainerBuilder $container)
    {
        $driver = DriverFactory::createDriver($type);
        $driver->load($container, $config);
    }
}
