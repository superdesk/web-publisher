<?php

/**
 * This file is part of the Superdesk Web Publisher Updater Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\UpdaterBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SWPUpdaterExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!empty($config['client'])) {
            $container->setParameter($this->getAlias().'.client.base_uri', $config['client']);
        }

        if (!empty($config['version_class'])) {
            $container->setParameter($this->getAlias().'.version_class', $config['version_class']);
        }

        if ($config['temp_dir'] === 'default') {
            $container->setParameter(
                $this->getAlias().'.temp_dir',
                $container->getParameter('kernel.cache_dir')
            );
        } else {
            $container->setParameter(
                $this->getAlias().'.temp_dir',
                $container->getParameter('kernel.root_dir').'/'.$config['temp_dir']
            );
        }

        if ($config['target_dir'] === 'default') {
            $container->setParameter(
                $this->getAlias().'.target_dir',
                $container->getParameter('kernel.root_dir').'/../'
            );
        } else {
            $container->setParameter(
                $this->getAlias().'.target_dir',
                $config['target_dir']
            );
        }
    }
}
