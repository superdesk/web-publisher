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

use SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\TenantAwareRouterCompilerPass;
use Symfony\Component\DependencyInjection\Reference;

class TenantAwareRouterCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $definition;
    private $pass;

    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $this->definition = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->setMethods(['setArguments', 'addMethodCall', 'setMethodCalls'])
            ->getMock();

        $this->pass = new TenantAwareRouterCompilerPass();
    }

    /**
     * @covers SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\TenantAwareRouterCompilerPass::process
     */
    public function testProcess()
    {
        $this->container->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap([
                ['swp_multi_tenancy.backend_type_phpcr', true],
                ['cmf_routing.backend_type_phpcr', true],
            ]));

        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('kernel.bundles')
            ->will($this->returnValue([
                'CmfRoutingBundle' => true,
            ]));

        $definition2 = clone $this->definition;

        $this->container->expects($this->any())
            ->method('getDefinition')
            ->will($this->returnValueMap([
                ['swp_multi_tenancy.tenant_aware_router', $this->definition],
                ['cmf_routing.dynamic_router', $definition2],
            ]));

        $this->definition->expects($this->once())
            ->method('setMethodCalls')
            ->will($this->returnValue($this->definition));

        $this->definition->expects($this->once())
            ->method('setArguments')
            ->will($this->returnValue($this->definition));

        $this->definition->expects($this->once())
            ->method('addMethodCall')
            ->with(
                'setPathBuilder',
                [new Reference('swp_multi_tenancy.path_builder')]
            );

        $this->pass->process($this->container);
    }

    /**
     * @covers SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\TenantAwareRouterCompilerPass::process
     */
    public function testProcessCMFBackendDisabled()
    {
        $this->container->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap([
                ['swp_multi_tenancy.backend_type_phpcr', true],
                ['cmf_routing.backend_type_phpcr', false],
            ]));

        $this->assertNull($this->pass->process($this->container));
    }

    /**
     * @covers SWP\Bundle\MultiTenancyBundle\DependencyInjection\Compiler\TenantAwareRouterCompilerPass::process
     */
    public function testProcessWithoutConfig()
    {
        $this->container->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap([
                ['swp_multi_tenancy.backend_type_phpcr', false],
                ['cmf_routing.backend_type_phpcr', false],
            ]));

        $this->assertNull($this->pass->process($this->container));
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\RuntimeException
     */
    public function testNoBundle()
    {
        $this->container->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap([
                ['swp_multi_tenancy.backend_type_phpcr', true],
                ['cmf_routing.backend_type_phpcr', true],
            ]));

        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('kernel.bundles')
            ->will($this->returnValue([]));

        $this->pass->process($this->container);
    }
}
