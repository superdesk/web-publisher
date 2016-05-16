<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle;

use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\ConfigurePrefixCandidatesCompilerPass;
use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\RegisterTenantFactoryCompilerPass;
use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\TenantAwareRouterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SWPMultiTenancyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ConfigurePrefixCandidatesCompilerPass());
        $container->addCompilerPass(new TenantAwareRouterCompilerPass());
        $container->addCompilerPass(new RegisterTenantFactoryCompilerPass());
    }
}
