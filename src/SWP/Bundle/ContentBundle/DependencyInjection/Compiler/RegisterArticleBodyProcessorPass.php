<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterArticleBodyProcessorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('swp_content_bundle.processor.article_body')) {
            return;
        }

        $definition = $container->getDefinition('swp_content_bundle.processor.article_body');
        $taggedServices = $container->findTaggedServiceIds('swp.processor.article_body');

        foreach ($taggedServices as $key => $taggedService) {
            $priority = (int) ($taggedService[0]['priority'] ?? 0);

            $definition->addMethodCall('addProcessor', [new Reference($key), $priority]);
        }
    }
}
