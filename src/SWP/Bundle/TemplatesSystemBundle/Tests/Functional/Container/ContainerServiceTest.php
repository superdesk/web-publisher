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

use SWP\Bundle\TemplatesSystemBundle\Tests\Functional\WebTestCase;
use SWP\Bundle\TemplatesSystemBundle\Service\ContainerService;

class ContainerServiceTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->initDatabase();
    }

    public function testDebugConstruct()
    {
        $this->createContainerService();
    }

    public function testProductionConstruct()
    {
        $this->createContainerService(false);
    }

    public function testGetRenderer()
    {
        $containerService = $this->createContainerService();
        $this->assertInstanceOf('\Twig_Environment', $containerService->getRenderer());
    }

    public function testCreateNewContainer()
    {
        $this->initDatabase();
        $containerService = $this->createContainerService();

        $containerParameters = [
            'height' => '400',
            'width' => '300',
            'cssClass' => 'col-md-12',
            'styles' => 'border: 1px solid red;',
            'visible' => true,
            'data' => [
                'key' => 'value',
            ],
        ];

        $containerEntity = $containerService->createNewContainer('test Container', $containerParameters);

        $this->assertEquals('400', $containerEntity->getHeight());
        $this->assertEquals('300', $containerEntity->getWidth());
        $this->assertEquals('col-md-12', $containerEntity->getCssClass());
        $this->assertEquals('border: 1px solid red;', $containerEntity->getStyles());
        $this->assertEquals(true, $containerEntity->getVisible());
        $this->assertEquals(1, count($containerEntity->getData()));
    }

    public function testGetContainerException()
    {
        $containerService = $this->createContainerService();
        $this->setExpectedException('\Exception');
        $containerService->getContainer('test container', [], false);
    }

    public function testGetContainer()
    {
        $containerService = $this->createContainerService();
        $containerEntity = $containerService->getContainer('test container', ['data' => ['key' => 'value']]);
        $this->assertInstanceOf('\SWP\Bundle\TemplatesSystemBundle\Container\SimpleContainer', $containerEntity);
    }

    private function createContainerService($debug = true)
    {
        return new ContainerService(
            $this->getContainer()->get('doctrine'),
            $this->getContainer()->get('event_dispatcher'),
            $this->getContainer(),
            $this->getContainer()->getParameter('kernel.cache_dir'),
            $debug
        );
    }
}
