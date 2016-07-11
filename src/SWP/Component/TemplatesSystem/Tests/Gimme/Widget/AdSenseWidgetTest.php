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
namespace SWP\Component\TemplatesSystem\Tests\Gimme\Widget;

use SWP\Component\TemplatesSystem\Gimme\Widget\GoogleAdSenseWidgetHandler;

class AdSenseWidgetTest extends \PHPUnit_Framework_TestCase
{
    private $widget;

    public function setUp()
    {
        $widgetEntity = new WidgetModel();
        $widgetEntity->setId(1);
        $widgetEntity->setParameters(['ad_client' => '1', 'ad_slot' => '1']);

        $this->widget = new GoogleAdSenseWidgetHandler($widgetEntity);
    }

    public function testCheckingVisibility()
    {
        $this->assertTrue($this->widget->isVisible());
    }

    public function testRendering()
    {
        $rendered = $this->widget->render();
        $this->assertContains($rendered, 'data-ad-client="1"');
        $this->assertContains($rendered, 'data-ad-slot="1"');
    }
}
