<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\DependencyInjection\Compiler;

use SWP\Bundle\CoreBundle\Theme\Locator\OrganizationThemesRecursiveFileLocator;
use Sylius\Bundle\ThemeBundle\Configuration\CompositeConfigurationProvider;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\FilesystemConfigurationProvider;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\JsonFileConfigurationLoader;
use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\ProcessingConfigurationLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class OrganizationThemesProviderPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $compositeConfigurationProvider = new Definition(CompositeConfigurationProvider::class);

        $recursiveFileLocator = new Definition(OrganizationThemesRecursiveFileLocator::class, [
            new Reference('sylius.theme.finder_factory'),
            new Reference('swp_core.uploader.theme'),
        ]);

        $configurationLoader = new Definition(ProcessingConfigurationLoader::class, [
            new Definition(JsonFileConfigurationLoader::class, [
                new Reference('sylius.theme.filesystem'),
            ]),
            new Reference('sylius.theme.configuration.processor'),
        ]);

        $configurationProvider = new Definition(FilesystemConfigurationProvider::class, [
            $recursiveFileLocator,
            $configurationLoader,
            'theme.json',
        ]);

        $compositeConfigurationProvider->addArgument([$configurationProvider]);
        $container->setDefinition('swp_core.organization.theme.configuration.provider', $compositeConfigurationProvider);
    }
}
