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

use SWP\Bundle\PaywallBundle\Doctrine\ORM\SubscriptionRepository;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Paywall\Adapter\PaymentsHubAdapter;
use SWP\Component\Paywall\Model\Subscription;
use SWP\Component\Paywall\Model\SubscriptionInterface;
use SWP\Component\Storage\Factory\Factory;
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
                ->arrayNode('persistence')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('subscription')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->cannotBeEmpty()->defaultValue(Subscription::class)->end()
                                                ->scalarNode('repository')->defaultValue(SubscriptionRepository::class)->end()
                                                ->scalarNode('interface')->defaultValue(SubscriptionInterface::class)->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
