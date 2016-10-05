<?php

/*
 * This file is part of the Superdesk Web Publisher Rule Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\RuleBundle\DependencyInjection\Compiler;

use Prophecy\Argument;
use SWP\Bundle\RuleBundle\DependencyInjection\Compiler\RegisterRuleApplicatorsPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @mixin RegisterRuleApplicatorsPass
 */
final class RegisterRuleApplicatorsPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RegisterRuleApplicatorsPass::class);
    }

    public function it_is_compiler_pass()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_processes(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('swp_rule.applicator_chain')->willReturn(true);
        $container->getDefinition('swp_rule.applicator_chain')->willReturn($definition);
        $container->findTaggedServiceIds('applicator.rule_applicator')->willReturn([
            'id' => [
                [
                    'alias' => 'alias',
                ],
            ],
        ]);

        $definition->addMethodCall('addApplicator', Argument::type('array'))->shouldBeCalled();

        $this->process($container);
    }

    public function it_does_not_process_anything_when_no_definition(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('swp_rule.applicator_chain')->willReturn(false);
        $container->getDefinition('swp_rule.applicator_chain')->willReturn($definition);
        $container->findTaggedServiceIds('applicator.rule_applicator')->willReturn([
            'id' => [
                [
                    'alias' => 'alias',
                ],
            ],
        ]);

        $definition->addMethodCall('addApplicator', Argument::type('array'))->shouldNotBeCalled();

        $this->process($container);
    }
}
