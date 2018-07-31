<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\DependencyInjection\Compiler;

use SWP\Bundle\CoreBundle\Twig\Cache\Strategy\LifetimeCacheStrategy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OverrideTwigContentCache extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $lifetimeStrategyDefinition = $this->getDefinitionIfExists($container, 'twig_cache.strategy.lifetime');
        $lifetimeStrategyDefinition->setClass(LifetimeCacheStrategy::class);
        $lifetimeStrategyDefinition->addArgument(new Reference('swp_multi_tenancy.tenant_context'));
        $lifetimeStrategyDefinition->addArgument(new Reference('swp_revision.context.revision'));
    }
}
