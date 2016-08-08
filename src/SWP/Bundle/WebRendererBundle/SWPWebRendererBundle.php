<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\WebRendererBundle;

use SWP\Bundle\StorageBundle\DependencyInjection\Bundle\Bundle;
use SWP\Bundle\StorageBundle\Drivers;
use SWP\Bundle\WebRendererBundle\Theme\Configuration\TenantableConfigurationSourceFactory;
use Sylius\Bundle\ThemeBundle\DependencyInjection\SyliusThemeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SWPWebRendererBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SyliusThemeExtension $themeExtension */
        $themeExtension = $container->getExtension('sylius_theme');
        $themeExtension->addConfigurationSourceFactory(new TenantableConfigurationSourceFactory());
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            Drivers::DRIVER_DOCTRINE_PHPCR_ODM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getModelClassNamespace()
    {
        return 'SWP\Bundle\WebRendererBundle\Doctrine\ODM\PHPCR';
    }
}
