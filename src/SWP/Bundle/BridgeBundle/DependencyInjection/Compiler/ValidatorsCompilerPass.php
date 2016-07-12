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
namespace SWP\Bundle\BridgeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ValidatorsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('swp_bridge.http_push.validator_chain')) {
            return;
        }

        $definition = $container->getDefinition('swp_bridge.http_push.validator_chain');

        foreach ($container->findTaggedServiceIds('validator.http_push_validator') as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addValidator',
                    array(new Reference($id), $attributes['alias'])
                );
            }
        }
    }
}
