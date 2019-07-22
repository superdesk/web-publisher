<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Configuration;

use SWP\Bundle\CoreBundle\Theme\Filesystem\JsonFileConfigurationLoader;
use SWP\Bundle\CoreBundle\Theme\Helper\ThemeHelper;
use SWP\Bundle\CoreBundle\Theme\Locator\TenantThemesRecursiveFileLocator;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeAssetProviderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationSourceFactoryInterface;
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
    public function buildConfiguration(ArrayNodeDefinition $node): void
    {
        $node
            ->fixXmlConfig('directory', 'directories')
                ->children()
                    ->scalarNode('filename')->defaultValue('theme.json')->cannotBeEmpty()->end()
                    ->scalarNode('local_themes_path')->defaultValue('%kernel.project_dir%')->end()
                    ->arrayNode('directories')
                        ->defaultValue([''])
                        ->requiresAtLeastOneElement()
                        ->prototype('scalar')
                    ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function initializeSource(ContainerBuilder $container, array $config)
    {
        $container->setParameter('swp.theme.configuration.filename', $config['filename']);
        $container->setParameter('swp.theme.configuration.default_directory', $config['directories'][0]);

        $recursiveFileLocator = new Definition(TenantThemesRecursiveFileLocator::class, [
            new Reference('sylius.theme.finder_factory'),
            $config['directories'],
            new Reference(ThemeAssetProviderInterface::class),
        ]);

        $themeConfigurationProcessor = $container->getDefinition('sylius.theme.configuration.processor');
        $themeConfigurationProcessor->replaceArgument(0, new Definition(ThemeConfiguration::class));

        $configurationLoader = new Definition(ProcessingConfigurationLoader::class, [
            new Definition(JsonFileConfigurationLoader::class, [
                new Reference(ThemeAssetProviderInterface::class),
            ]),
            $themeConfigurationProcessor,
        ]);

        $configurationProvider = new Definition(TenantableConfigurationProvider::class, [
            $recursiveFileLocator,
            $configurationLoader,
            $config['filename'],
            new Definition(ThemeHelper::class, [
                $config['directories'],
            ]),
        ]);

        return $configurationProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'tenantable';
    }
}
