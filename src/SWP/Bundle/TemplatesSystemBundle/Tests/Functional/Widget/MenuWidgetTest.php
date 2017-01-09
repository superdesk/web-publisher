<?php
/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Tests\Functional\Widget;

use SWP\Bundle\TemplatesSystemBundle\Tests\Functional\WebTestCase;
use SWP\Bundle\TemplatesSystemBundle\Widget\MenuWidgetHandler;
use SWP\Component\TemplatesSystem\Tests\Gimme\Model\WidgetModel;

class MenuWidgetTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->initDatabase();

        $this->loadFixtures(
            [
                'SWP\Bundle\TemplatesSystemBundle\Tests\Fixtures\ORM\LoadMenusData',
                'SWP\Bundle\TemplatesSystemBundle\Tests\Fixtures\ORM\LoadMenuNodesData',
            ], 'default'
        );
    }

    public function testMenuWidget()
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setParameters(['menu_name' => 'test']);
        $widgetHandler = new MenuWidgetHandler($widgetModel, $this->getContainer());

        $content = $widgetHandler->render();

        $this->assertContains('Default menu template', $content);
        $this->assertContainsRenderedWidget($content);
    }

    public function testMenuWidgetCustomTemplate()
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setParameters(['menu_name' => 'test', 'template_name' => 'custom_menu_template.html.twig']);
        $widgetHandler = new MenuWidgetHandler($widgetModel, $this->getContainer());

        $content = $widgetHandler->render();

        $this->assertContains('Custom menu template', $content);
        $this->assertContainsRenderedWidget($content);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMenuWidgetWhenCustomTemplateDoesNotExist()
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setParameters(['menu_name' => 'test', 'template_name' => 'test_menu.html.twig']);
        $widgetHandler = new MenuWidgetHandler($widgetModel, $this->getContainer());

        $widgetHandler->render();
    }

    public function testMenuWidgetWhenCustomTemplateIsNotSet()
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setParameters(['menu_name' => 'test', 'template_name' => null]);

        $widgetHandler = new MenuWidgetHandler($widgetModel, $this->getContainer());

        $content = $widgetHandler->render();

        $this->assertContains('Default menu template', $content);
        $this->assertContainsRenderedWidget($content);
    }

    private function assertContainsRenderedWidget($content)
    {
        $this->assertContains('<a href="http://example.com/home">Home</a>', $content);
        $this->assertContains('<a href="http://example.com/contact">Contact</a>', $content);
        $this->assertContains('<a href="http://example.com/contact/sub">Sub Contact</a>', $content);
    }
}
