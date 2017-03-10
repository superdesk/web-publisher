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

use SWP\Bundle\TemplatesSystemBundle\Service\ContainerService;
use SWP\Bundle\TemplatesSystemBundle\Tests\Functional\WebTestCase;

class ContainerServiceTest extends WebTestCase
{
    public function testDebugConstruct()
    {
        $this->createContainerService();
    }

    public function testCreateNewContainer()
    {
        $this->initDatabase();
        $containerService = $this->createContainerService();

        $containerParameters = [
            'cssClass' => 'col-md-12',
            'styles' => 'border: 1px solid red;',
            'visible' => true,
            'data' => [
                'key' => 'value',
            ],
        ];

        $containerEntity = $containerService->createContainer('test Container', $containerParameters);

        $this->assertEquals('col-md-12', $containerEntity->getCssClass());
        $this->assertEquals('border: 1px solid red;', $containerEntity->getStyles());
        $this->assertEquals(true, $containerEntity->getVisible());
        $this->assertEquals(1, count($containerEntity->getData()));
    }

    public function testUpdateContainer()
    {
        $this->initDatabase();
        $containerService = $this->createContainerService();

        $containerParameters = [
            'cssClass' => 'col-md-12',
            'styles' => 'border: 1px solid red;',
            'visible' => true,
            'data' => [
                'key' => 'value',
            ],
        ];
        $containerEntity = $containerService->createContainer('test Container', $containerParameters);

        $containerService->updateContainer($containerEntity, [
            'key' => 'value',
            'key2' => 'value2',
        ]);
        $this->assertEquals(2, count($containerEntity->getData()));
    }

    private function createContainerService()
    {
        return new ContainerService(
            $this->getContainer()->get('swp.object_manager.container'),
            $this->getContainer()->get('event_dispatcher'),
            $this->getContainer()->get('service_container')
        );
    }
}
