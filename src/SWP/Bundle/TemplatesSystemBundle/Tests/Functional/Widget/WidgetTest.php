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

use SWP\Bundle\TemplatesSystemBundle\Model\ContainerWidget;
use SWP\Bundle\TemplatesSystemBundle\Model\WidgetModel;
use SWP\Bundle\TemplatesSystemBundle\Tests\Functional\WebTestCase;

class WidgetTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->initDatabase();
    }

    public function testWidget()
    {
        $widget = $this->createWidget();
        self::assertInstanceOf($this->getContainer()->getParameter('swp.model.widget_model.class'), $widget);

        $widget->setType(WidgetModel::TYPE_HTML);
        $widget->setName('Test Widget');

        $objectManager = $this->getContainer()->get('swp.object_manager.widget_model');

        $objectManager->persist($widget);
        $objectManager->flush();
    }

    public function testAddingWidgetToContainer()
    {
        $container = $this->getContainer()->get('swp.factory.container')->create();
        $objectManager = $this->getContainer()->get('swp.object_manager.widget_model');

        $container->setName('test_container');
        $objectManager->persist($container);

        $widget = $this->createWidget();
        $widget->setName('Test Widget');
        $objectManager->persist($widget);
        $containerWidget = new ContainerWidget($container, $widget);
        $objectManager->persist($containerWidget);
        $container->addWidget($containerWidget);

        $objectManager->flush();

        $repository = $this->getContainer()->get('swp.repository.container');
        /** @var ContainerInterface $containerFromDatabase */
        $containerFromDatabase = $repository->getByName('test_container')->getQuery()->getSingleResult();
        self::assertCount(1, $containerFromDatabase->getWidgets());
    }

    private function createWidget()
    {
        $widgetClassName = $this->getContainer()->getParameter('swp.model.widget_model.class');
        /* @var WidgetModel $widget */
        return new $widgetClassName();
    }
}
