<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use SWP\Bundle\TemplateEngineBundle\Model\Container;
use SWP\Bundle\TemplateEngineBundle\Model\ContainerData;
use SWP\Bundle\TemplateEngineBundle\Model\ContainerWidget;
use SWP\Bundle\TemplateEngineBundle\Model\WidgetModel;
use SWP\Bundle\TemplateEngineBundle\Service\BranchService;

class BranchServiceTest extends WebTestCase
{
    /**
     * @var BranchService
     */
    protected $branchService;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->runCommand('doctrine:schema:drop', ['--force' => true, '--env' => 'test'], true);
        $this->runCommand('doctrine:schema:update', ['--force' => true, '--env' => 'test'], true);

        $this->loadFixtureFiles([
            '@SWPFixturesBundle/Resources/fixtures/ORM/test/tenant.yml',
        ]);

        $this->runCommand('doctrine:phpcr:repository:init', ['--env' => 'test'], true);

        $this->branchService = $this->getContainer()->get('swp_template_engine_branch');
        $this->objectManager = $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testCreateAndPublishContainerBranch()
    {
        $container = $this->createTestContainer('testicicle', ['data' => ['jiminy' => 'cricket']]);
        $target = $this->branchService->createBranchedContainer($container);

        /** @var ArrayCollection $data */
        $data = $target->getData();

        /** @var ContainerData $datum */
        $datum = $data->first();
        $datum->setValue('football');

        $published = $this->branchService->publishBranchedContainer($target->getId());
        $data = $published->getData();

        $this->assertEquals(1, $data->count());
        $this->assertEquals('football', $data->first()->getValue());

        $this->objectManager->remove($container);
        $this->objectManager->flush();
    }

    public function testCreateAndPublishWidgetBranch()
    {
        $widgetModel = $this->createTestWidget('Widget zero');
        $target = $this->branchService->createBranchedWidgetModel($widgetModel);

        $target->setParameters(['goolie' => 'goolie goolie']);

        $published = $this->branchService->publishBranchedWidgetModel($target->getId());

        $parameters = $published->getParameters();
        $this->assertFalse(isset($parameters['gingan']));
        $this->assertEquals($parameters['goolie'], 'goolie goolie');

        $this->objectManager->remove($published);
        $this->objectManager->flush();
    }

    public function testCreateAndPublishContainerBranchWithWidget()
    {
        $container = $this->createTestContainer('testicicle', ['data' => ['jiminy' => 'cricket']]);
        $widget0 = $this->createTestWidget('Widget zero');
        $this->createTestContainerWidget($container, $widget0);
        $widget1 = $this->createTestWidget('Widget one');
        $this->createTestContainerWidget($container, $widget1);
        $this->objectManager->flush();

        /** @var Container $target */
        $target = $this->branchService->createBranchedContainer($container);

        // Change order of widgets in branch
        $containerWidgets = $target->getWidgets();
        $containerWidget = $containerWidgets->first();
        $containerWidget->setPosition(2);
        $this->objectManager->flush();

        // Publish branch
        $published = $this->branchService->publishBranchedContainer($target->getId());

        // Check widgets are reordered
        $containerWidgets = $target->getWidgets();
        $containerWidget = $containerWidgets->first();
        $this->assertEquals($containerWidget->getWidget()->getName(), $widget0->getName());

        $this->objectManager->remove($published);
        $this->objectManager->remove($widget0);
        $this->objectManager->remove($widget1);
        $this->objectManager->flush();
    }

    public function testCreateAndPublishWidgetInContainers()
    {
        $container0 = $this->createTestContainer('testicicle', ['data' => ['jiminy' => 'cricket']]);
        $container1 = $this->createTestContainer('womble', ['data' => ['uncle' => 'bulgaria']]);
        $widget = $this->createTestWidget('Widget zero');
        $this->createTestContainerWidget($container0, $widget);
        $this->createTestContainerWidget($container1, $widget);
        $this->objectManager->flush();

        /** @var WidgetModel $target */
        $target = $this->branchService->createBranchedWidgetModel($widget);

        $target->setParameters(['goolie' => 'goolie goolie']);

        $published = $this->branchService->publishBranchedWidgetModel($target->getId());

        /** @var ContainerWidget $containerWidget */
        $containerWidget = $container0->getWidgets()->first();
        $parameters = $containerWidget->getWidget()->getParameters();

        $this->assertTrue(isset($parameters['goolie']));
        $this->assertEquals($parameters['goolie'], 'goolie goolie');

        $this->objectManager->remove($published);
        $this->objectManager->remove($container0);
        $this->objectManager->remove($container1);
        $this->objectManager->flush();
    }

    private function createTestContainer($name, $data = array())
    {
        $containerService = $this->getContainer()->get('swp_template_engine_container');
        $container = $containerService->createNewContainer($name, $data);

        return $container;
    }

    private function createTestWidget($name)
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setName($name);
        $widgetModel->setType(WidgetModel::TYPE_HTML);
        $widgetModel->setVisible(true);
        $widgetModel->setParameters(['gingan' => 'gooly']);
        $this->objectManager->persist($widgetModel);
        $this->objectManager->flush();

        return $widgetModel;
    }

    private function createTestContainerWidget(Container $container, WidgetModel $widgetModel)
    {
        $containerWidget = new ContainerWidget($container, $widgetModel);
        $container->addWidget($containerWidget);
        $widgetModel->addContainer($containerWidget);
        $this->objectManager->persist($containerWidget);
        $this->objectManager->flush();

        return $containerWidget;
    }
}
