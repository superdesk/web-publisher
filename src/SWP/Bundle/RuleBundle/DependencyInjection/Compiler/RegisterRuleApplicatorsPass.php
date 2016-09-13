<?php

/**
 * This file is part of the Superdesk Web Publisher Rule Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\RuleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RegisterRuleApplicatorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swp_rule.applicator_chain')) {
            return;
        }

        $definition = $container->getDefinition('swp_rule.applicator_chain');
        $taggedServices = $container->findTaggedServiceIds('applicator.rule_applicator');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addApplicator', [
                new Reference($id),
            ]);
        }
    }
}
