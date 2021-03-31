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

namespace spec\SWP\Bundle\CoreBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideThemeAssetsInstallerPass;
use SWP\Bundle\CoreBundle\Theme\Asset\AssetsInstaller;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Sylius\Bundle\ThemeBundle\Asset\Installer\AssetsInstallerInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @mixin OverrideThemeAssetsInstallerPass
 */
class OverrideThemeAssetsInstallerPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(OverrideThemeAssetsInstallerPass::class);
    }

    public function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_overrides_default_theme_assets_installer(
        ContainerBuilder $container,
        Definition $definition
    ) {
        $container->hasDefinition(AssetsInstallerInterface::class)->willReturn(true);
        $container->getDefinition(AssetsInstallerInterface::class)->willReturn($definition);

        $definition->setArgument(4, new Reference(ThemeHierarchyProviderInterface::class))->shouldBeCalled();
        $definition->setClass(AssetsInstaller::class)->shouldBeCalled();

        $this->process($container);
    }

    public function it_should_not_override_default_theme_assets_installer(
        ContainerBuilder $container,
        Definition $definition
    ) {
        $container->hasDefinition(AssetsInstallerInterface::class)->willReturn(false);
        $container->getDefinition(AssetsInstallerInterface::class)->shouldNotBeCalled();

        $definition->setArgument(4, new Reference(ThemeHierarchyProviderInterface::class))->shouldNotBeCalled();
        $definition->setClass(AssetsInstaller::class)->shouldNotBeCalled();

        $this->process($container)->shouldReturn(null);
    }
}
