<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\CoreBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideThemeAssetsInstallerPass;
use SWP\Bundle\CoreBundle\Theme\Asset\AssetsInstaller;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @mixin OverrideThemeAssetsInstallerPass
 */
class OverrideThemeAssetsInstallerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OverrideThemeAssetsInstallerPass::class);
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_overrides_default_theme_assets_installer(
        ContainerBuilder $container,
        Definition $definition
    ) {
        $container->hasDefinition('sylius.theme.asset.assets_installer')->willReturn(true);
        $container->getDefinition('sylius.theme.asset.assets_installer')->willReturn($definition);

        $definition->setClass(AssetsInstaller::class)->shouldBeCalled();

        $this->process($container);
    }

    function it_should_not_override_default_theme_assets_installer(
        ContainerBuilder $container,
        Definition $definition
    ) {
        $container->hasDefinition('sylius.theme.asset.assets_installer')->willReturn(false);
        $container->getDefinition('sylius.theme.asset.assets_installer')->shouldNotBeCalled();

        $definition->setClass(AssetsInstaller::class)->shouldNotBeCalled();

        $this->process($container)->shouldReturn(null);
    }
}
