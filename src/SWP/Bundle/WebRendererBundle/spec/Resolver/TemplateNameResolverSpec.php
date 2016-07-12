<?php

namespace spec\SWP\Bundle\WebRendererBundle\Resolver;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\WebRendererBundle\Resolver\TemplateNameResolverInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;

class TemplateNameResolverSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\WebRendererBundle\Resolver\TemplateNameResolver');
    }

    public function it_should_implement_interface()
    {
        $this->shouldImplement(TemplateNameResolverInterface::class);
    }

    public function it_should_resolve_template_name_from_article(ArticleInterface $article, RouteInterface $route)
    {
        $this->resolveFromArticle($article)->shouldReturn('article.html.twig');

        $article->getTemplateName()->willReturn('test.html.twig');
        $article->getRoute()->willReturn(null);
        $this->resolveFromArticle($article)->shouldReturn('test.html.twig');

        $article->getTemplateName()->willReturn(null);
        $route->getTemplateName()->willReturn('test2.html.twig');
        $article->getRoute()->willReturn($route);
        $this->resolveFromArticle($article)->shouldReturn('test2.html.twig');
    }

    public function it_should_resolve_template_name_from_route(RouteInterface $route)
    {
        $route->getTemplateName()->willReturn('test2.html.twig');
        $this->resolveFromRoute($route)->shouldReturn('test2.html.twig');

        $route->getTemplateName()->willReturn(null);
        $this->resolveFromRoute($route)->shouldReturn('article.html.twig');
    }

    public function it_should_resolve(RouteInterface $route, ArticleInterface $article)
    {
        $route->getTemplateName()->willReturn('test2.html.twig');
        $this->resolve($route)->shouldReturn('test2.html.twig');

        $article->getTemplateName()->willReturn('article2.html.twig');
        $article->getRoute()->willReturn(null);
        $this->resolve($article)->shouldReturn('article2.html.twig');

        $this->resolve(null)->shouldReturn('article.html.twig');
    }
}
