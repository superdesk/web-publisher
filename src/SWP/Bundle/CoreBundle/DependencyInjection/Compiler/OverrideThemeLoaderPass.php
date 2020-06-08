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

use SWP\Bundle\CoreBundle\Theme\Loader\TenantAwareThemeLoader;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OverrideThemeLoaderPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->overrideDefinitionClassIfExists(
            $container,
            ThemeLoaderInterface::class,
            TenantAwareThemeLoader::class
        );
    }
}
