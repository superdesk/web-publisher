<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher User Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\UserBundle;

use SWP\Bundle\StorageBundle\DependencyInjection\Bundle\Bundle;
use SWP\Bundle\StorageBundle\Drivers;
use SWP\Bundle\UserBundle\DependencyInjection\Compiler\DisableLastLoginListenerPass;
use SWP\Bundle\UserBundle\DependencyInjection\Compiler\OverrideFosMailerPass;
use SWP\Component\Storage\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SWPUserBundle extends Bundle
{

    /**
     * Default format of mapping files.
     *
     * @var string
     */
    protected $mappingFormat = BundleInterface::MAPPING_XML;

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new OverrideFosMailerPass());
        $container->addCompilerPass(new DisableLastLoginListenerPass());
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
        return 'SWP\Bundle\UserBundle\Model';
    }
}
