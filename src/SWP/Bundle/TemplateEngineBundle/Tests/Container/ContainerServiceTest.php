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
namespace SWP\Bundle\TemplateEngineBundle\Tests\Container;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\Bundle\TemplateEngineBundle\Service\ContainerService;

class ContainerServiceTest extends WebTestCase
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
        $this->createAndPopulateDatabase();
        $tenantContext = $this->getContainer()->get('swp_multi_tenancy.tenant_context');
        $containerService = $this->createContainerService();

        $containerParameters = [
            'height'   => '400',
            'width'    => '300',
            'cssClass' => 'col-md-12',
            'styles'   => 'border: 1px solid red;',
            'visible'  => true,
            'data'     => [
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
        $this->assertEquals($tenantContext->getTenant(), $containerEntity->getTenant());
    }

    public function testGetContainerException()
    {
        $this->createAndPopulateDatabase();
        $containerService = $this->createContainerService();
        $this->setExpectedException('\Exception');
        $containerService->getContainer('test container', [], false);
    }

    public function testGetContainer()
    {
        $this->createAndPopulateDatabase();
        $containerService = $this->createContainerService();
        $containerEntity = $containerService->getContainer('test container', ['data' => ['key' => 'value']]);
        $this->assertInstanceOf('\SWP\Bundle\TemplateEngineBundle\Container\SimpleContainer', $containerEntity);
    }

    private function createAndPopulateDatabase()
    {
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/tenant.yml',
        ]);
    }

    private function createContainerService($debug = true)
    {
        return new ContainerService(
            $this->getContainer(),
            $this->getContainer()->getParameter('kernel.cache_dir'),
            $debug
        );
    }
}
