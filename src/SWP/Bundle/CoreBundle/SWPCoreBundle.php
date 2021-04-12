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

namespace SWP\Bundle\CoreBundle;

use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\AddCustomTwigCachePass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OrganizationThemesProviderPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideArticleSourceAdderPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideAssetLocationResolver;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideBodyListenerPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideDynamicRouterPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideEmailVerifierPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideEmbeddedImageProcessorPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideMediaFactoryPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideMediaManagerPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverridePackagePreviewTokenFactoryPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideSerializerPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideSettingsManagerPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideThemeAssetsInstallerPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideThemeFactoryPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideThemeLoaderPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideThemePathResolverPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideThemeRepositoryPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideThemeTranslatorPass;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideTwigContentCache;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterOutputChannelAdapterPass;
use SWP\Bundle\StorageBundle\DependencyInjection\Bundle\Bundle;
use SWP\Bundle\StorageBundle\Drivers;
use SWP\Bundle\CoreBundle\Theme\Configuration\TenantableConfigurationSourceFactory;
use Sylius\Bundle\ThemeBundle\DependencyInjection\SyliusThemeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SWPCoreBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SyliusThemeExtension $themeExtension */
        $themeExtension = $container->getExtension('sylius_theme');
        $themeExtension->addConfigurationSourceFactory(new TenantableConfigurationSourceFactory());
        $container->addCompilerPass(new OverrideThemeFactoryPass());
        $container->addCompilerPass(new OverrideThemePathResolverPass());
        $container->addCompilerPass(new OverrideThemeAssetsInstallerPass());
        $container->addCompilerPass(new OverrideDynamicRouterPass());
        $container->addCompilerPass(new OverrideMediaManagerPass());
        $container->addCompilerPass(new OverrideAssetLocationResolver());
        $container->addCompilerPass(new OverrideThemeLoaderPass());
        $container->addCompilerPass(new OverrideThemeTranslatorPass());
        $container->addCompilerPass(new OverrideSettingsManagerPass());
        $container->addCompilerPass(new AddCustomTwigCachePass());
        $container->addCompilerPass(new OverrideArticleSourceAdderPass());
        $container->addCompilerPass(new OrganizationThemesProviderPass());
        $container->addCompilerPass(new OverrideThemeRepositoryPass());
        $container->addCompilerPass(new OverrideTwigContentCache());
        $container->addCompilerPass(new OverridePackagePreviewTokenFactoryPass());
        $container->addCompilerPass(new RegisterOutputChannelAdapterPass());
        $container->addCompilerPass(new OverrideMediaFactoryPass());
        $container->addCompilerPass(new OverrideSerializerPass());
        $container->addCompilerPass(new OverrideEmbeddedImageProcessorPass());
        $container->addCompilerPass(new OverrideBodyListenerPass());
        $container->addCompilerPass(new OverrideEmailVerifierPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            Drivers::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getModelClassNamespace()
    {
        return 'SWP\Bundle\CoreBundle\Model';
    }
}
