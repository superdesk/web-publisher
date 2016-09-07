<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle;

use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\ConfigurePrefixCandidatesCompilerPass;
use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\RegisterOrganizationFactoryPass;
use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\RegisterTenantFactoryCompilerPass;
use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\TenantAwareRouterCompilerPass;
use SWP\Bundle\StorageBundle\DependencyInjection\Bundle\Bundle;
use SWP\Bundle\StorageBundle\Drivers;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SWPMultiTenancyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ConfigurePrefixCandidatesCompilerPass());
        $container->addCompilerPass(new TenantAwareRouterCompilerPass());
        $container->addCompilerPass(new RegisterTenantFactoryCompilerPass());
        $container->addCompilerPass(new RegisterOrganizationFactoryPass());
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
        return 'SWP\Component\MultiTenancy\Model';
    }
}
