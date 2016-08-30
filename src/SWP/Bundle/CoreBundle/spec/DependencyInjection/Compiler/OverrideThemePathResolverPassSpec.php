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
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideThemePathResolverPass;
use SWP\Bundle\CoreBundle\Theme\Asset\PathResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @mixin OverrideThemePathResolverPass
 */
class OverrideThemePathResolverPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OverrideThemePathResolverPass::class);
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_overrides_default_theme_path_resolver(
        ContainerBuilder $container,
        Definition $definition
    ) {
        $container->hasDefinition('sylius.theme.asset.path_resolver')->willReturn(true);
        $container->getDefinition('sylius.theme.asset.path_resolver')->willReturn($definition);

        $definition->setClass(PathResolver::class)->shouldBeCalled();

        $this->process($container);
    }

    function it_should_not_override_default_theme_path_resolver(
        ContainerBuilder $container,
        Definition $definition
    ) {
        $container->hasDefinition('sylius.theme.asset.path_resolver')->willReturn(false);
        $container->getDefinition('sylius.theme.asset.path_resolver')->shouldNotBeCalled();

        $definition->setClass(PathResolver::class)->shouldNotBeCalled();

        $this->process($container)->shouldReturn(null);
    }
}
