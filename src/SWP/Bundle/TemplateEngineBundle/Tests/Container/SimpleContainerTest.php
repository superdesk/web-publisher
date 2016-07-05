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

use SWP\Bundle\TemplateEngineBundle\Container\SimpleContainer;
use SWP\Bundle\TemplateEngineBundle\Model\WidgetModel;
use SWP\Component\TemplatesSystem\Gimme\Widget\HtmlWidgetHandler;

class SimpleContainerTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    const OPEN_TAG_TEMPLATE = '<div id="swp_container_{{ id }}" class="swp_container {{ class }}" style="{% if height %}height: {{ height }}px;{% endif %}{% if width %}width: {{width}}px;{% endif %}{{styles}}"{% for value in data %} data-{{value.getKey()}}="{{value.getValue()}}"{% endfor %} >';
    const CLOSE_TAG_TEMPLATE = '</div>';

    private function getRenderer()
    {
        return new \Twig_Environment(
            new \Twig_Loader_Array([
                'open_tag' => self::OPEN_TAG_TEMPLATE,
                'close_tag' => self::CLOSE_TAG_TEMPLATE,
            ])
        );
    }

    public function setUp()
    {
        $containerEntity = new \SWP\Bundle\TemplateEngineBundle\Model\Container();
        $containerEntity->setId(1);

        $this->container = new SimpleContainer($containerEntity, $this->getRenderer());
    }

    public function testCheckingVisibility()
    {
        $this->assertTrue($this->container->isVisible());
    }

    public function testSimpleRendering()
    {
        $this->assertEquals($this->container->renderOpenTag(), '<div id="swp_container_1" class="swp_container " style="" >');
        $this->assertEquals($this->container->renderCloseTag(), '</div>');
    }

    public function testAdvancedRendering()
    {
        $containerEntity = new \SWP\Bundle\TemplateEngineBundle\Model\Container();
        $containerEntity->setId(2);
        foreach (['key1' => true, 'key2' => 'false', 'key3' => false] as $key => $value) {
            $containerData = new \SWP\Bundle\TemplateEngineBundle\Model\ContainerData($key, $value);
            $containerEntity->addData($containerData);
        }
        $containerEntity->setWidth(400);
        $containerEntity->setHeight(300);
        $containerEntity->setCssClass('simple-css-class');
        $containerEntity->setStyles('border: 1px solid red;');
        $containerEntity->setName('simple_container');
        $container = new \SWP\Bundle\TemplateEngineBundle\Container\SimpleContainer($containerEntity, $this->getRenderer());

        $this->assertEquals($container->renderOpenTag(), '<div id="swp_container_2" class="swp_container simple-css-class" style="height: 300px;width: 400px;border: 1px solid red;" data-key1="1" data-key2="false" data-key3="" >');
    }

    public function testWidgets()
    {
        $widgetEntity = new WidgetModel();
        $widgetEntity->setParameters(['html_body' => 'simple html body']);
        $widget = new HtmlWidgetHandler($widgetEntity);

        $this->assertEquals($this->container->setWidgets([$widget, $widget]), $this->container);
        $this->assertEquals($this->container->hasWidgets(), true);
        $this->assertEquals($this->container->renderWidgets(), <<<'EOF'
simple html body
simple html body
EOF
        );
    }
}
