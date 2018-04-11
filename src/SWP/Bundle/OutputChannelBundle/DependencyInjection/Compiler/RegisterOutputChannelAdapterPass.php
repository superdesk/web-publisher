<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Output Channel Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\OutputChannelBundle\DependencyInjection\Compiler;

use SWP\Component\OutputChannel\Provider\AdapterProviderChain;
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
        if (!$container->hasDefinition(AdapterProviderChain::class)) {
            return;
        }

        $definition = $container->getDefinition(AdapterProviderChain::class);
        $taggedServices = $container->findTaggedServiceIds('swp.output_channel_adapter');

        foreach ($taggedServices as $key => $taggedService) {
            $definition->addMethodCall('addProvider', [new Reference($key)]);
        }
    }
}
