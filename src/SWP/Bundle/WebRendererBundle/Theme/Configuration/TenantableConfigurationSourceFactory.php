<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\WebRendererBundle\Theme\Configuration;

use SWP\Bundle\WebRendererBundle\Theme\Helper\PathHelper;
use SWP\Bundle\WebRendererBundle\Theme\Locator\TenantableFileLocator;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationSourceFactoryInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\FilesystemConfigurationProvider;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\JsonFileConfigurationLoader;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\ProcessingConfigurationLoader;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class TenantableConfigurationSourceFactory implements ConfigurationSourceFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildConfiguration(ArrayNodeDefinition $node)
    {
        $node
            ->fixXmlConfig('directory', 'directories')
                ->children()
                    ->scalarNode('filename')->defaultValue('theme.json')->cannotBeEmpty()->end()
                    ->arrayNode('directories')
                        ->defaultValue(['%kernel.root_dir%/themes'])
                        ->requiresAtLeastOneElement()
                        ->performNoDeepMerging()
                        ->prototype('scalar')
                    ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function initializeSource(ContainerBuilder $container, array $config)
    {
        $pathHelper = new Definition(PathHelper::class, [
            new Reference('swp_multi_tenancy.tenant_context'),
        ]);

        $tenantableFileLocator = new Definition(TenantableFileLocator::class, [
            new Reference('sylius.theme.finder_factory'),
            $config['directories'],
            $pathHelper,
        ]);

        $configurationLoader = new Definition(ProcessingConfigurationLoader::class, [
            new Definition(JsonFileConfigurationLoader::class, [
                new Reference('sylius.theme.filesystem'),
            ]),
            new Reference('sylius.theme.configuration.processor'),
        ]);

        $configurationProvider = new Definition(FilesystemConfigurationProvider::class, [
            $tenantableFileLocator,
            $configurationLoader,
            $config['filename'],
        ]);

        return $configurationProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tenantable';
    }
}
