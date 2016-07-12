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
use SWP\Bundle\BridgeBundle\DependencyInjection\Compiler\ValidatorsCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @mixin ValidatorsCompilerPass
 */
class ValidatorsCompilerPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ValidatorsCompilerPass::class);
    }

    public function it_is_compiler_pass()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_processes(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('swp_bridge.http_push.validator_chain')->willreturn(true);
        $container->getDefinition('swp_bridge.http_push.validator_chain')->willreturn($definition);
        $container->findTaggedServiceIds('validator.http_push_validator')->willreturn([
            'id' => [
                [
                    'alias' => 'alias',
                ],
            ],
        ]);

        $definition->addMethodCall('addValidator', Argument::type('array'))->shouldBeCalled();

        $this->process($container);
    }
}
