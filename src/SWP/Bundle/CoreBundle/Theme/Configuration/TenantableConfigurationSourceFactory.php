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

use SWP\Bundle\CoreBundle\Theme\Helper\ThemeHelper;
use SWP\Bundle\CoreBundle\Theme\Locator\TenentThemesRecursiveFileLocator;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationSourceFactoryInterface;
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
    public function buildConfiguration(ArrayNodeDefinition $node): void
    {
        $node
            ->fixXmlConfig('directory', 'directories')
                ->children()
                    ->scalarNode('filename')->defaultValue('theme.json')->cannotBeEmpty()->end()
                    ->arrayNode('directories')
                        ->defaultValue(['%kernel.project_dir%/app/themes'])
                        ->requiresAtLeastOneElement()
                        ->performNoDeepMerging()
                        ->prototype('scalar')
                    ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function initializeSource(ContainerBuilder $container, array $config)
    {
        $recursiveFileLocator = new Definition(TenentThemesRecursiveFileLocator::class, [
            new Reference('sylius.theme.finder_factory'),
            $config['directories'],
        ]);

        $themeConfigurationProcessor = $container->getDefinition('sylius.theme.configuration.processor');
        $themeConfigurationProcessor->replaceArgument(0, new Definition(ThemeConfiguration::class));

        $configurationLoader = new Definition(ProcessingConfigurationLoader::class, [
            new Definition(JsonFileConfigurationLoader::class, [
                new Reference('sylius.theme.filesystem'),
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

        $container->setParameter('swp.theme.configuration.filename', $config['filename']);
        $container->setParameter('swp.theme.configuration.default_directory', $config['directories'][0]);

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
