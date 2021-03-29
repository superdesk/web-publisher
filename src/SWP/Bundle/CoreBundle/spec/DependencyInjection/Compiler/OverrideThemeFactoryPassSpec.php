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
use SWP\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideThemeFactoryPass;
use SWP\Bundle\CoreBundle\Theme\Factory\ThemeFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;

/**
 * @mixin OverrideThemeFactoryPass
 */
class OverrideThemeFactoryPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(OverrideThemeFactoryPass::class);
    }

    public function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_overrides_default_theme_factory(
        ContainerBuilder $container,
        Definition $definition
    ) {
        $container->hasDefinition(ThemeFactoryInterface::class)->willReturn(true);
        $container->getDefinition(ThemeFactoryInterface::class)->willReturn($definition);

        $definition->setClass(ThemeFactory::class)->shouldBeCalled();

        $this->process($container);
    }

    public function it_should_not_override_default_theme_factory(
        ContainerBuilder $container,
        Definition $definition
    ) {
        $container->hasDefinition(ThemeFactoryInterface::class)->willReturn(false);
        $container->getDefinition(ThemeFactoryInterface::class)->shouldNotBeCalled();

        $definition->setClass(ThemeFactory::class)->shouldNotBeCalled();

        $this->process($container)->shouldReturn(null);
    }
}
