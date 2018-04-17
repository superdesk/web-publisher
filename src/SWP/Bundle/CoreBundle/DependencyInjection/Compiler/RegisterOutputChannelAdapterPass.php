<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\DependencyInjection\Compiler;

use SWP\Bundle\CoreBundle\Adapter\CompositeOutputChannelAdapter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterOutputChannelAdapterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(CompositeOutputChannelAdapter::class)) {
            return;
        }

        $definition = $container->getDefinition(CompositeOutputChannelAdapter::class);
        $taggedServices = $container->findTaggedServiceIds('swp.output_channel_adapter');

        foreach ($taggedServices as $key => $taggedService) {
            $definition->addMethodCall('addAdapter', [new Reference($key)]);
        }
    }
}
