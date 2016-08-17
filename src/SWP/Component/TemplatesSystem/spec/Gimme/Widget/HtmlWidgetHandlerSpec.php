<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Component\TemplatesSystem\Gimme\Widget;

use PhpSpec\ObjectBehavior;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
use SWP\Component\TemplatesSystem\Gimme\Widget\HtmlWidgetHandler;

/**
 * @mixin HtmlWidgetHandler
 */
class HtmlWidgetHandlerSpec extends ObjectBehavior
{
    public function let(WidgetModelInterface $widgetModel)
    {
        $widgetModel->getParameters()->willReturn(['html_body' => 'sample html']);
        $this->beConstructedWith($widgetModel);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(HtmlWidgetHandler::class);
    }

    public function it_should_render()
    {
        $this->render()->shouldReturn('sample html');
    }

    public function it_should_return_empty_string_when_no_parameter($widgetModel)
    {
        $widgetModel->getParameters()->willReturn([]);

        $this->render()->shouldReturn('');
    }

    public function it_should_check_if_it_is_visible($widgetModel)
    {
        $widgetModel->getVisible()->willReturn(true);
        $this->isVisible()->shouldReturn(true);

        $widgetModel->getVisible()->willReturn(false);
        $this->isVisible()->shouldReturn(false);
    }
}
