<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\TemplateEngineBundle\Tests\Container;

use SWP\TemplateEngineBundle\Container\SimpleContainer;
use SWP\TemplateEngineBundle\Model\Widget;
use SWP\TemplatesSystem\Gimme\Widget\HtmlWidget;
use SWP\TemplateEngineBundle\Service\ContainerService;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class WidgetControllerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
    }

    public function testDebugConstruct()
    {
        $containerService = new ContainerService(
            $this->getContainer()->get('doctrine'),
            $this->getContainer()->getParameter('kernel.cache_dir'),
            true
        );
    }

    public function testProductionConstruct()
    {
        $containerService = new ContainerService(
            $this->getContainer()->get('doctrine'),
            $this->getContainer()->getParameter('kernel.cache_dir'),
            false
        );
    }

    public function testGetRenderer()
    {
        $containerService = new ContainerService(
            $this->getContainer()->get('doctrine'),
            $this->getContainer()->getParameter('kernel.cache_dir')
        );

        $this->assertInstanceOf('\Twig_Environment', $containerService->getRenderer());
    }

    public function testCreateNewContainer()
    {
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:schema:create', ['--env' => 'test'], true);

        $containerService = new ContainerService(
            $this->getContainer()->get('doctrine'),
            $this->getContainer()->getParameter('kernel.cache_dir'),
            true
        );

        $containerParameters = [
            'height' => '400',
            'width' => '300',
            'cssClass' => 'col-md-12',
            'styles' => 'border: 1px solid red;',
            'visible' => true,
            'data' => [
                'key' => 'value'
            ]
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
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:schema:create', ['--env' => 'test'], true);

        $containerService = new ContainerService(
            $this->getContainer()->get('doctrine'),
            $this->getContainer()->getParameter('kernel.cache_dir'),
            true
        );

        $this->setExpectedException('\Exception');
        $containerEntity = $containerService->getContainer('test container', [], false);
    }

    public function testGetContainer()
    {
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:schema:create', ['--env' => 'test'], true);

        $containerService = new ContainerService(
            $this->getContainer()->get('doctrine'),
            $this->getContainer()->getParameter('kernel.cache_dir'),
            true
        );

        $containerEntity = $containerService->getContainer('test container', ['data' => ['key' => 'value']]);
        $this->assertInstanceOf('\SWP\TemplateEngineBundle\Container\SimpleContainer', $containerEntity);
    }
}
