<?php

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
