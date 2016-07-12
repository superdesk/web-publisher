<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\TemplatesSystem\Tests\Gimme\Container;

use SWP\Component\TemplatesSystem\Gimme\Widget\HtmlWidgetHandler;
use SWP\Component\TemplatesSystem\Tests\Gimme\Model\WidgetModel;

class HtmlWidgetTest extends \PHPUnit_Framework_TestCase
{
    private $widget;

    public function setUp()
    {
        $widgetEntity = new WidgetModel();
        $widgetEntity->setId(1);
        $widgetEntity->setParameters(['html_body' => 'simple html body']);

        $this->widget = new HtmlWidgetHandler($widgetEntity);
    }

    public function testCheckingVisibility()
    {
        $this->assertTrue($this->widget->isVisible());
    }

    public function testRendering()
    {
        $this->assertEquals($this->widget->render(), 'simple html body');
    }
}
