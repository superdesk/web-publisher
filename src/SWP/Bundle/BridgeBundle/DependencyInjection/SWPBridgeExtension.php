<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge for the Content API.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\BridgeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SWPBridgeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $defaultOptions = [];
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $mainKeys = ['api', 'auth'];
        foreach ($mainKeys as $mainKey) {
            if (isset($config[$mainKey])) {
                foreach ($config[$mainKey] as $key => $value) {
                    if (!empty($value)) {
                        $container->setParameter(sprintf('%s.%s.%s', $this->getAlias(), $mainKey, $key), $value);
                    }
                }
            }
        }

        if (isset($config['options']) && is_array($config['options'])) {
            $defaultOptions = $config['options'];
        }
        $container->setParameter($this->getAlias().'.options', $defaultOptions);
    }
}
