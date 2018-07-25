<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Paywall Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\PaywallBundle\DependencyInjection;

use SWP\Component\Paywall\Adapter\PaymentsHubAdapter;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('swp_paywall')
            ->children()
                ->scalarNode('adapter')
                    ->defaultValue(PaymentsHubAdapter::class)
                    ->info('Subscriptions System Adapter')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
