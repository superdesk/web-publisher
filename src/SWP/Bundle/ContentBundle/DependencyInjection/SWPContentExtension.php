<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\DependencyInjection;

use SWP\Bundle\ContentBundle\DependencyInjection\Driver\DriverFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SWPContentExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($config['persistence']['phpcr']['enabled']) {
            $this->loadPhpcrProvider($config['persistence']['phpcr'], $container);
            $loader->load('phpcr.yml');
        }

        if ($config['persistence']['orm']['enabled']) {
            $this->loadOrmProvider($config['persistence']['orm'], $container);
        }

        //$container->setAlias('swp.manager.article', 'swp.manager.article.default');
    }

    private function loadPhpcrProvider($config, ContainerBuilder $container)
    {
        $container->setParameter('swp_content.dynamic.persistence.phpcr.manager_name', $config['object_manager_name']);
        $container->setParameter('swp_content.backend_type_phpcr', true);
        $this->loadDriver('phpcr', $container, $config);
        $container->setAlias('swp.manager.article', $config['article_manager_name']);
    }

    private function loadOrmProvider($config, ContainerBuilder $container)
    {
        $container->setParameter('swp_content.dynamic.persistence.phpcr.manager_name', $config['object_manager_name']);
        $container->setParameter('swp_content.backend_type_orm', true);
        $this->loadDriver('orm', $container, $config);
        $container->setAlias('swp.manager.article', $config['article_manager_name']);
    }

    private function loadDriver($type, $container, $config)
    {
        $driver = DriverFactory::createDriver($type);
        $driver->load($container, $config);
    }
}
