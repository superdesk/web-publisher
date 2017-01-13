<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle;

use SWP\Bundle\StorageBundle\Drivers;
use SWP\Bundle\TemplatesSystemBundle\DependencyInjection\Compiler\RegisterContainerDataFactory;
use SWP\Bundle\TemplatesSystemBundle\DependencyInjection\Compiler\RegisterContainerWidgetFactory;
use SWP\Bundle\TemplatesSystemBundle\DependencyInjection\ContainerBuilder\MetaLoaderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SWP\Bundle\StorageBundle\DependencyInjection\Bundle\Bundle;

class SWPTemplatesSystemBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MetaLoaderCompilerPass());
        $container->addCompilerPass(new RegisterContainerDataFactory());
        $container->addCompilerPass(new RegisterContainerWidgetFactory());
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            Drivers::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getModelClassNamespace()
    {
        return 'SWP\Bundle\TemplatesSystemBundle\Model';
    }
}
