<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\ConfigurePrefixCandidatesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConfigurePrefixCandidatesCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @param ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigurePrefixCandidatesCompilerPass());
    }

    /**
     * @covers SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\ConfigurePrefixCandidatesCompilerPass::process
     */
    public function testProcessPHPCRBackendDisabled()
    {
        $this->container->setParameter('cmf_routing.backend_type_phpcr', true);

        $this->compile();

        $this->assertContainerBuilderHasParameter('cmf_routing.backend_type_phpcr');
    }

    /**
     * @covers SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\ConfigurePrefixCandidatesCompilerPass::process
     */
    public function testProcessCMFBackendDisabled()
    {
        $this->container->setParameter('swp_multi_tenancy.backend_type_phpcr', true);

        $this->compile();

        $this->assertContainerBuilderHasParameter('swp_multi_tenancy.backend_type_phpcr');
    }

    /**
     * @covers SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\ConfigurePrefixCandidatesCompilerPass::process
     */
    public function testProcess()
    {
        $this->container->setParameter('swp_multi_tenancy.backend_type_phpcr', true);
        $this->container->setParameter('cmf_routing.backend_type_phpcr', true);
        $this->container->setParameter('kernel.bundles', [
            'CmfRoutingBundle' => true,
        ]);
        $this->container->setParameter('swp_multi_tenancy.persistence.phpcr.route_basepaths', ['routes']);
        $this->container->setParameter(
            'swp_multi_tenancy.prefix_candidates.class',
            'SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR\PrefixCandidates'
        );

        $collectingService = new Definition();
        $this->setDefinition('cmf_routing.phpcr_candidates_prefix', $collectingService);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'cmf_routing.phpcr_candidates_prefix',
            'SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR\PrefixCandidates'
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'cmf_routing.phpcr_candidates_prefix',
            'setPathBuilder',
            [
                new Reference('swp_multi_tenancy.path_builder'),
            ]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'cmf_routing.phpcr_candidates_prefix',
            'setRoutePathsNames',
            [
                $this->container->getParameter('swp_multi_tenancy.persistence.phpcr.route_basepaths'),
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\RuntimeException
     */
    public function testProcessWhenNoBundle()
    {
        $this->container->setParameter('swp_multi_tenancy.backend_type_phpcr', true);
        $this->container->setParameter('cmf_routing.backend_type_phpcr', true);

        $this->container->setParameter('kernel.bundles', []);

        $this->compile();

        $this->assertContainerBuilderHasParameter('swp_multi_tenancy.backend_type_phpcr');
    }
}
