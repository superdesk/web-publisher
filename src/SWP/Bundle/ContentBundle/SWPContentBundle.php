<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle;

use SWP\Bundle\ContentBundle\DependencyInjection\Compiler\RegisterArticleFactoryPass;
use SWP\Bundle\ContentBundle\DependencyInjection\Compiler\RegisterRouteFactoryPass;
use SWP\Bundle\StorageBundle\DependencyInjection\Bundle\Bundle;
use SWP\Bundle\StorageBundle\Drivers;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SWPContentBundle extends Bundle
{
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
        return 'SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR';
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterArticleFactoryPass());
        $container->addCompilerPass(new RegisterRouteFactoryPass());
    }
}
