<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle\DependencyInjection;

use SWP\Bundle\StorageBundle\Drivers;
use SWP\Bundle\StorageBundle\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SWPUserExtension extends Extension
{

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('controllers.yml');

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('flash_notifications.xml');
        $loader->load('mailer.xml');

        if ($config['persistence']['orm']['enabled']) {
            $this->registerStorage(Drivers::DRIVER_DOCTRINE_ORM, $config['persistence']['orm']['classes'], $container);
        }

        if (!empty($config['resetting'])) {
            $this->loadResetting($config['resetting'], $container, $loader, $config['from_email']);
        }
    }

    private function loadResetting(array $config, ContainerBuilder $container, XmlFileLoader $loader, array $fromEmail)
    {
        $loader->load('resetting.xml');

        if (isset($config['email']['from_email'])) {
            // overwrite the global one
            $fromEmail = $config['email']['from_email'];
            unset($config['email']['from_email']);
        }
        $container->setParameter('swp_user.resetting.email.from_email', [$fromEmail['address'] => $fromEmail['sender_name']]);

        $this->remapParametersNamespaces($config, $container, [
            '' => [
                'retry_ttl' => 'swp_user.resetting.retry_ttl',
                'token_ttl' => 'swp_user.resetting.token_ttl',
            ],
            'email' => 'swp_user.resetting.email.%s',
            'form' => 'swp_user.resetting.form.%s',
        ]);
    }

    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }
}
