<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\DependencyInjection;

use SWP\Bundle\StorageBundle\DependencyInjection\Extension\Extension;
use SWP\Bundle\StorageBundle\Drivers;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SWPCoreExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('twig.yml');
        $loader->load('composite_publishing.yml');
        $loader->load('rules.yml');
        $loader->load('form.yml');
        $loader->load('output_channel_adapter.yml');
        $loader->load('websocket.yml');
        $loader->load('commands.yml');
        $loader->load('controllers.yaml');
        $loader->load('subscribers.yaml');
        $loader->load('message_handlers.yaml');

        $this->loadDeviceListener($config, $loader);

        $config = $container->resolveEnvPlaceholders($config);

        if (!empty($config['superdesk_servers'])) {
            $container->setParameter('superdesk_servers', $config['superdesk_servers'][0]);
        }

        $this->registerStorage(Drivers::DRIVER_DOCTRINE_ORM, $config['persistence']['orm']['classes'], $container);
    }

    private function loadDeviceListener(array $config, Loader\YamlFileLoader $loader)
    {
        if ($config['device_listener']['enabled']) {
            $loader->load('device_listener.yml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = $container->getExtensionConfig('doctrine_cache');
        $config[0]['providers']['main_cache']['type'] = '%env(DOCTRINE_CACHE_DRIVER)%';
        $config[0]['providers']['main_cache']['namespace'] = '%kernel.project_dir%';

        $config = $container->resolveEnvPlaceholders(
            $config,
            true
        );

        $container->prependExtensionConfig('doctrine_cache', $config[0]);

        $fosHttpCacheConfig['proxy_client']['varnish']['http']['servers'] = '%env(json:resolve:CACHE_SERVERS)%';
        $fosHttpCacheConfig = $container->resolveEnvPlaceholders(
            $fosHttpCacheConfig,
            true
        );

        $container->prependExtensionConfig('fos_http_cache', $fosHttpCacheConfig);

        $nelmioCorsConfig = [
            'defaults' => [
                'allow_origin' => [
                    'https://superdesk.cloud.funkedigital.de',
                ],
            ],
        ];
        $nelmioCorsConfig = $container->resolveEnvPlaceholders(
            $nelmioCorsConfig,
            true
        );

        $container->prependExtensionConfig('nelmio_cors', $nelmioCorsConfig);
    }
}
