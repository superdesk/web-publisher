<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\DependencyInjection;

use SWP\Bundle\StorageBundle\DependencyInjection\Extension\Extension;
use SWP\Bundle\StorageBundle\Drivers;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SWPMultiTenancyExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $backendEnabled = false;

        if ($config['persistence']['phpcr']['enabled']) {
            $this->loadPhpcr($config['persistence']['phpcr'], $loader, $container);
            $this->registerStorage(
                Drivers::DRIVER_DOCTRINE_PHPCR_ODM,
                $config['persistence']['phpcr']['classes'],
                $container
            );

            $backendEnabled = true;
        }

        if ($config['persistence']['orm']['enabled']) {
            $this->registerStorage(
                Drivers::DRIVER_DOCTRINE_ORM,
                $config['persistence']['orm']['classes'],
                $container
            );

            $backendEnabled = true;
        }

        if (!$backendEnabled) {
            throw new InvalidConfigurationException('You need to enable one of the peristence backends (phpcr or orm)');
        }

        if ($config['use_orm_listeners']) {
            $loader->load('listeners.yml');
        }
    }

    public function loadPhpcr($config, YamlFileLoader $loader, ContainerBuilder $container)
    {
        $keys = [
            'basepath' => 'basepath',
            'route_basepaths' => 'route_basepaths',
            'content_basepath' => 'content_basepath',
            'menu_basepath' => 'menu_basepath',
            'media_basepath' => 'media_basepath',
            'tenant_aware_router_class' => 'router.class',
        ];

        foreach ($keys as $sourceKey => $targetKey) {
            $container->setParameter(
                $this->getAlias().'.persistence.phpcr.'.$targetKey,
                $config[$sourceKey]
            );
        }

        array_push($config['route_basepaths'], $config['content_basepath'], $config['menu_basepath'], $config['media_basepath']);

        $container->setParameter(
            $this->getAlias().'.persistence.phpcr.base_paths',
            $config['route_basepaths']
        );

        $loader->load('phpcr.yml');
    }
}
