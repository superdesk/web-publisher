<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Seo Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SeoBundle\DependencyInjection;

use SWP\Bundle\StorageBundle\DependencyInjection\Extension\Extension;
use SWP\Bundle\StorageBundle\Drivers;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;

class SWPSeoExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        if ($config['persistence']['orm']['enabled']) {
            $this->registerStorage(Drivers::DRIVER_DOCTRINE_ORM, $config['persistence']['orm']['classes'], $container);
        }
    }
}
