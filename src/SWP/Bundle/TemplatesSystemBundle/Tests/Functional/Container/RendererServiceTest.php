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

namespace SWP\Bundle\TemplatesSystemBundle\Tests\Functional\Container;

use SWP\Bundle\TemplatesSystemBundle\Container\ContainerRenderer;
use SWP\Bundle\TemplatesSystemBundle\Tests\Functional\WebTestCase;
use SWP\Bundle\TemplatesSystemBundle\Service\RendererService;

class RendererServiceTest extends WebTestCase
{
    public function testDebugConstruct()
    {
        $this->createRendererService();
    }

    public function testProductionConstruct()
    {
        $this->createRendererService(false);
    }

    public function testGetRenderer()
    {
        $rendererService = $this->createRendererService();
        self::assertInstanceOf(ContainerRenderer::class, $rendererService->getContainerRenderer('test_container'));
    }

    public function testGetContainerRendererException()
    {
        $rendererService = $this->createRendererService();
        $this->expectException(\Exception::class);
        $rendererService->getContainerRenderer('test container', [], false);
    }

    public function testGetContainerRenderer()
    {
        $rendererService = $this->createRendererService();
        $containerRenderer = $rendererService->getContainerRenderer('test container', ['data' => ['key' => 'value']]);
        self::assertInstanceOf('\SWP\Bundle\TemplatesSystemBundle\Container\ContainerRenderer', $containerRenderer);
    }

    private function createRendererService($debug = true)
    {
        return new RendererService(
            $this->getContainer()->get('service_container'),
            $this->getContainer()->getParameter('kernel.cache_dir'),
            $debug,
            $this->getContainer()->get('swp_template_engine.container.service'),
            $this->getContainer()->get('swp.provider.container'),
            $this->getContainer()->get('swp.factory.container_renderer')
        );
    }
}
