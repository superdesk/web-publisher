<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\RegisterTenantFactoryCompilerPass;
use SWP\Component\MultiTenancy\Factory\TenantFactory;
use SWP\Component\MultiTenancy\Model\Tenant;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterTenantFactoryCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @param ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterTenantFactoryCompilerPass());
    }

    /**
     * @covers SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\RegisterTenantFactoryCompilerPass::process
     */
    public function testProcess()
    {
        $this->container->setParameter('swp_multi_tenancy.factory.tenant.class', TenantFactory::class);
        $this->container->setParameter('swp_multi_tenancy.tenant.class', Tenant::class);

        $collectingService = new Definition();
        $this->setDefinition('swp_multi_tenancy.factory.tenant', $collectingService);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'swp_multi_tenancy.factory.tenant',
            TenantFactory::class
        );
    }

    public function testProcessWhenNoParam()
    {
        $this->container->setParameter('swp_multi_tenancy.tenant.class', Tenant::class);

        $collectingService = new Definition();
        $this->setDefinition('swp_multi_tenancy.factory.tenant', $collectingService);

        $this->compile();

        $this->assertContainerBuilderHasParameter('swp_multi_tenancy.tenant.class');
    }
}
