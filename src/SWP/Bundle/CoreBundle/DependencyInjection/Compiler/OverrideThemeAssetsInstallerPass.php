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

use SWP\Bundle\CoreBundle\Theme\Asset\AssetsInstaller;
use Sylius\Bundle\ThemeBundle\Asset\Installer\AssetsInstallerInterface;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OverrideThemeAssetsInstallerPass extends AbstractOverridePass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->overrideDefinitionClassIfExists(
            $container,
            AssetsInstallerInterface::class,
            AssetsInstaller::class
        );

        if($newDefinition = $this->getDefinitionIfExists($container, AssetsInstallerInterface::class)) {
            $newDefinition->setArgument(4, new Reference(ThemeHierarchyProviderInterface::class));
        }
    }
}
