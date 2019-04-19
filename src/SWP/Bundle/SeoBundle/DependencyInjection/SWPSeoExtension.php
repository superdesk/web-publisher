<?php

declare(strict_types=1);

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
        //$loader->load('services.yml');

        if ($config['persistence']['orm']['enabled']) {
            $this->registerStorage(Drivers::DRIVER_DOCTRINE_ORM, $config['persistence']['orm']['classes'], $container);
        }
    }
}
