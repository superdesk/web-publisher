<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\TemplateEngineBundle\DependencyInjection\ContainerBuilder;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class MetaLoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('swp_template_engine_loader_chain')) {
            return;
        }

        $definition = $container->findDefinition(
            'swp_template_engine_loader_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'swp.meta_loader'
        );
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addLoader',
                array(new Reference($id))
            );
        }
    }
}
