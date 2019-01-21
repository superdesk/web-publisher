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

use SWP\Bundle\CoreBundle\Context\ScopeContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OverrideSettingsManagerPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->overrideDefinitionClassIfExists(
            $container,
            'swp_settings.context.scope',
            ScopeContext::class
        );

        $newDefinition = $this->getDefinitionIfExists($container, 'swp_settings.context.scope');
        $newDefinition->setArgument(0, new Reference('swp_multi_tenancy.tenant_context'));
        $newDefinition->setArgument(1, new Reference('swp_core.theme.context.tenant_aware'));
    }
}
