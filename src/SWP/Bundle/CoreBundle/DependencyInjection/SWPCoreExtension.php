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

use SWP\Bundle\AnalyticsBundle\EventListener\MetricsListener;
use SWP\Bundle\CoreBundle\Detection\DeviceDetection;
use SWP\Bundle\CoreBundle\Enhancer\RouteEnhancer;
use SWP\Bundle\CoreBundle\Resolver\TemplateNameResolver;
use SWP\Bundle\CoreBundle\Theme\TenantAwareThemeContext;
use SWP\Bundle\MultiTenancyBundle\Context\TenantContext;
use SWP\Bundle\MultiTenancyBundle\EventListener\TenantableListener;
use SWP\Bundle\MultiTenancyBundle\Query\Filter\TenantableFilter;
use SWP\Bundle\MultiTenancyBundle\Routing\TenantAwareRouter;
use SWP\Bundle\StorageBundle\DependencyInjection\Extension\Extension;
use SWP\Bundle\StorageBundle\Drivers;
use SWP\Component\MultiTenancy\Model\TenantAwareTrait;
use SWP\Component\MultiTenancy\Resolver\TenantResolver;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\EventListener\RouterListener;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SWPCoreExtension extends Extension
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
        $this->loadDeviceListener($config, $loader);

        $this->registerStorage(Drivers::DRIVER_DOCTRINE_ORM, $config['persistence']['orm']['classes'], $container);

        $this->addClassesToCompile(array(
            TenantAwareThemeContext::class,
            TenantAwareRouter::class,
            TenantAwareTrait::class,
            TenantResolver::class,
            TenantContext::class,
            RouteEnhancer::class,
            RouterListener::class,
            TemplateNameResolver::class,
            TenantableListener::class,
            TenantableFilter::class,
            MetricsListener::class,
            Context::class,
        ));
    }

    private function loadDeviceListener(array $config, Loader\YamlFileLoader $loader)
    {
        if ($config['device_listener']['enabled']) {
            $loader->load('device_listener.yml');
            $this->addClassesToCompile(array(
                DeviceDetection::class,
            ));
        }
    }
}
