<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * Some parts of that file were taken from the Liip/ThemeBundle
 * (c) Liip AG
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\CoreBundle\Resolver;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Resolver\TemplateNameResolver;
use SWP\Bundle\CoreBundle\Resolver\TemplateNameResolverInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;

/**
 * @mixin TemplateNameResolver
 */
class TemplateNameResolverSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\CoreBundle\Resolver\TemplateNameResolver');
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
        $route->getType()->willReturn(RouteInterface::TYPE_CONTENT);
        $article->getRoute()->willReturn($route);
        $this->resolveFromArticle($article)->shouldReturn('test2.html.twig');
    }

    public function it_should_resolve_template_name_on_collection_routes(ArticleInterface $article, RouteInterface $route)
    {
        $article->getTemplateName()->willReturn(null);
        $route->getTemplateName()->willReturn('test2.html.twig');
        $route->getType()->willReturn(RouteInterface::TYPE_COLLECTION);
        $article->getRoute()->willReturn($route);

        $this->resolveFromArticle($article)->shouldReturn('article.html.twig');
    }

    public function it_should_resolve_template_name_from_route(RouteInterface $route)
    {
        $route->getTemplateName()->willReturn('test2.html.twig');
        $route->getType()->willReturn(RouteInterface::TYPE_CONTENT);
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
