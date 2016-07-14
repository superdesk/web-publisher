<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\BridgeBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\BridgeBundle\DependencyInjection\Compiler\TransformersCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @mixin TransformersCompilerPass
 */
class TransformersCompilerPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(TransformersCompilerPass::class);
    }

    public function it_is_compiler_pass()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_processes(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('swp_bridge.http_push.transformer_chain')->willreturn(true);
        $container->getDefinition('swp_bridge.http_push.transformer_chain')->willreturn($definition);
        $container->findTaggedServiceIds('transformer.http_push_transformer')->willreturn([
            'id' => [
                [
                    'alias' => 'alias',
                ],
            ],
        ]);

        $definition->replaceArgument(0, Argument::type('array'))->shouldBeCalled();

        $this->process($container);
    }
}
