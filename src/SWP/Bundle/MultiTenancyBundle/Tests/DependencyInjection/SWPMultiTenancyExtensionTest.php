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
namespace SWP\Bundle\MultiTenancyBundle\Tests\DependencyInjection;

use SWP\Bundle\MultiTenancyBundle\DependencyInjection\SWPMultiTenancyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class SWPMultiTenancyExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers SWP\Bundle\MultiTenancyBundle\SWPMultiTenancyBundle
     * @covers SWP\Bundle\MultiTenancyBundle\DependencyInjection\SWPMultiTenancyExtension::load
     * @covers SWP\Bundle\MultiTenancyBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @covers SWP\Bundle\MultiTenancyBundle\DependencyInjection\SWPMultiTenancyExtension::loadPhpcr
     */
    public function testLoad()
    {
        $container = $this->createContainer();
        $loader = $this->createLoader();
        $config = $this->getConfig();

        $loader->load([$config], $container);

        $this->assertTrue($container->getParameter('swp_multi_tenancy.backend_type_phpcr'));
        $this->assertEquals('/swp', $container->getParameter('swp_multi_tenancy.persistence.phpcr.basepath'));
        $this->assertEquals(
            'SWP\Bundle\MultiTenancyBundle\Document\Site',
            $container->getParameter('swp_multi_tenancy.persistence.phpcr.site_document.class')
        );

        $this->assertEquals(
            'SWP\Bundle\MultiTenancyBundle\Document\Page',
            $container->getParameter('swp_multi_tenancy.persistence.phpcr.document.class')
        );

        $this->assertEquals('content', $container->getParameter('swp_multi_tenancy.persistence.phpcr.content_basepath'));
        $this->assertEquals(
            'SWP\Bundle\MultiTenancyBundle\Routing\TenantAwareRouter',
            $container->getParameter('swp_multi_tenancy.persistence.phpcr.router.class')
        );

        $this->assertEquals(
           ['routes1', 'routes2'],
           $container->getParameter('swp_multi_tenancy.persistence.phpcr.route_basepaths')
       );

        $this->assertEquals(
            ['routes1', 'routes2', 'content'],
            $container->getParameter('swp_multi_tenancy.persistence.phpcr.base_paths')
        );

        $this->assertEquals(
            'SWP\Component\MultiTenancy\Model\Tenant',
            $container->getParameter('swp_multi_tenancy.tenant.class')
        );

        $this->assertEquals(
            'SWP\Component\MultiTenancy\Factory\TenantFactory',
            $container->getParameter('swp_multi_tenancy.factory.tenant.class')
        );

        $this->assertTrue($container->hasParameter('swp_multi_tenancy.backend_type_phpcr'));
    }

    public function testLoadWhenPHPCRDisabled()
    {
        $container = $this->createContainer();
        $loader = $this->createLoader();
        $config = $this->getConfig();
        $config['persistence']['phpcr']['enabled'] = false;
        $loader->load([$config], $container);

        $this->assertFalse($container->hasParameter('swp_multi_tenancy.backend_type_phpcr'));
    }

    protected function createLoader()
    {
        return new SWPMultiTenancyExtension();
    }

    protected function getConfig()
    {
        return [
            'persistence' => [
                'phpcr' => [
                    'enabled'          => true,
                    'content_basepath' => 'content',
                    'route_basepaths'  => ['routes1', 'routes2'],
                ],
            ],
        ];
    }

    protected function createContainer(array $data = [])
    {
        return new ContainerBuilder(new ParameterBag($data));
    }
}
