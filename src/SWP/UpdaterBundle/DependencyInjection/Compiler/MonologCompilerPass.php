<?php

/**
 * This file is part of the Superdesk Web Publisher Updater Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\UpdaterBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This pass enables separate Monolog channel for the bundle.
 */
class MonologCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('swp_updater.monolog_channel')) {
            return;
        }

        $bundles = $container->getParameter('kernel.bundles');

        if (!array_key_exists('MonologBundle', $bundles)) {
            throw new RuntimeException(
                'You have enabled the "monolog_channel" option but the MonologBundle is not registered'
            );
        }

        $loaderDef = $container->getDefinition('swp_updater.manager');
        $loaderDef->addArgument(new Reference('monolog.logger.updater'));
    }
}
