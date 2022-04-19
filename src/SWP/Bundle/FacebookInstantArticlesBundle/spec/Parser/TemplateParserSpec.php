<?php

namespace spec\SWP\Bundle\FacebookInstantArticlesBundle\Parser;

use Facebook\InstantArticles\Elements\InstantArticle;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\FacebookInstantArticlesBundle\Parser\TemplateParser;
use Twig\Environment;

class TemplateParserSpec extends ObjectBehavior
{
    public function let(Environment $templating)
    {
        $templating->render(Argument::type('string'))
            ->willReturn('<!doctype html><meta charset=utf-8><title>shortest html5</title>');
        $this->beConstructedWith($templating);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TemplateParser::class);
    }

    public function it_should_render_fbia_template()
    {
        $this->renderTemplate()->shouldBeString();
    }

    public function it_will_parse_template_and_create_instant_article()
    {
        $this->parse()->shouldReturnAnInstanceOf(InstantArticle::class);
        $this->parse(Argument::type('string'))->shouldReturnAnInstanceOf(InstantArticle::class);
    }
}
