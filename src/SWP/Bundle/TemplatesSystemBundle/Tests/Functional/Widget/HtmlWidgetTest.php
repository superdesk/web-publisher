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
use SWP\Bundle\TemplatesSystemBundle\Widget\HtmlWidgetHandler;
use SWP\Component\TemplatesSystem\Tests\Gimme\Model\WidgetModel;

class HtmlWidgetTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
    }

    public function testSimpleHtmlWidget()
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setParameters(['html_body' => '<h1>Test header</h1>']);
        $widgetHandler = new HtmlWidgetHandler($widgetModel, $this->getContainer());

        $content = $widgetHandler->render();

        $this->assertContains('<h1>Test header</h1>', $content);
    }

    public function testHtmlWidgetWithTemplate()
    {
        $widgetModel = new WidgetModel();
        $widgetModel->setParameters(['html_body' => '<h1>Test header</h1>', 'template_name' => 'html_widget_template.html.twig']);
        $widgetHandler = new HtmlWidgetHandler($widgetModel, $this->getContainer());
        $content = $widgetHandler->render();

        $this->assertContains('<h1>Test header</h1>
<p>extra html content from template</p>', $content);
    }
}
