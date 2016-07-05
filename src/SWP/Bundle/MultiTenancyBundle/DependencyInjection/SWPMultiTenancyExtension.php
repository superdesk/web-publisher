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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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

        if ($config['persistence']['phpcr']['enabled']) {
            $this->loadPhpcr($config['persistence']['phpcr'], $loader, $container);
            $container->setParameter($this->getAlias().'.backend_type_phpcr', true);
        }

        $container->setParameter(
            $this->getAlias().'.tenant.class',
            $config['resources']['tenant']['classes']['model']
        );

        $container->setParameter(
            $this->getAlias().'.factory.tenant.class',
            $config['resources']['tenant']['classes']['factory']
        );
    }

    public function loadPhpcr($config, YamlFileLoader $loader, ContainerBuilder $container)
    {
        $keys = [
            'basepath'                  => 'basepath',
            'route_basepaths'           => 'route_basepaths',
            'content_basepath'          => 'content_basepath',
            'site_document_class'       => 'site_document.class',
            'tenant_aware_router_class' => 'router.class',
            'document_class'            => 'document.class',
        ];

        foreach ($keys as $sourceKey => $targetKey) {
            $container->setParameter(
                $this->getAlias().'.persistence.phpcr.'.$targetKey,
                $config[$sourceKey]
            );
        }

        array_push($config['route_basepaths'], $config['content_basepath']);

        $container->setParameter(
            $this->getAlias().'.persistence.phpcr.base_paths',
            $config['route_basepaths']
        );

        $loader->load('phpcr.yml');
    }
}
