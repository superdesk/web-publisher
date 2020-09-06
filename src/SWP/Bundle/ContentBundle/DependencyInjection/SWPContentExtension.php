<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\DependencyInjection;

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
class SWPContentExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (Configuration::AWS_ADAPTER === $config['media_storage_adapter']) {
            $loader->load('asset_aws_storage.yaml');
        } else {
            $loader->load('asset_local_storage.yaml');
        }

        $loader->load('services.yml');
        $loader->load('controllers.yaml');
        $loader->load('listeners.yaml');

        if ($config['persistence']['orm']['enabled']) {
            $this->registerStorage(Drivers::DRIVER_DOCTRINE_ORM, $config['persistence']['orm']['classes'], $container);
            $loader->load('providers.orm.yml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = [
            [
                'adapters' => [
                    'fallback_adapter' => [
                        'fallback' => [
                            'mainAdapter' => '%env(FS_MAIN_ADAPTER)%',
                            'fallback' => Configuration::LOCAL_ADAPTER,
                            'forceCopyOnMain' => false,
                        ],
                    ],
                ],
            ],
        ];

        $config = $container->resolveEnvPlaceholders(
            $config,
            true
        );

        $mainAdapter = $config[0]['adapters']['fallback_adapter']['fallback']['mainAdapter'];

        $container->prependExtensionConfig('oneup_flysystem', $config[0]);
        $container->prependExtensionConfig($this->getAlias(), [
            'media_storage_adapter' => $mainAdapter,
        ]);
    }
}
