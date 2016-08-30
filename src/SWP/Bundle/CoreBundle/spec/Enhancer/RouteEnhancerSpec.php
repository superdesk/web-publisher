<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\CoreBundle\Enhancer;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Enhancer\RouteEnhancer;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use SWP\Bundle\CoreBundle\Resolver\TemplateNameResolver;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Component\TemplatesSystem\Gimme\Loader\LoaderInterface;

/**
 * @mixin RouteEnhancer
 */
class RouteEnhancerSpec extends ObjectBehavior
{
    public function let(LoaderInterface $metaLoader, Context $context)
    {
        $this->beConstructedWith(new TemplateNameResolver(), $metaLoader, $context);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RouteEnhancer::class);
    }

    public function it_should_implement_interface()
    {
        $this->shouldImplement(RouteEnhancerInterface::class);
    }

    public function it_should_set_template_name(RouteInterface $route, Article $article)
    {
        $this->setTemplateName(null, [])->shouldReturn([
            RouteObjectInterface::TEMPLATE_NAME => 'article.html.twig',
        ]);

        $this->setTemplateName(null, [RouteObjectInterface::ROUTE_OBJECT => $route])->shouldReturn([
            RouteObjectInterface::ROUTE_OBJECT => $route,
            RouteObjectInterface::TEMPLATE_NAME => 'article.html.twig',
        ]);

        $route->getTemplateName()->willReturn('test.html.twig');
        $this->setTemplateName(null, [RouteObjectInterface::ROUTE_OBJECT => $route])->shouldReturn([
            RouteObjectInterface::ROUTE_OBJECT => $route,
            RouteObjectInterface::TEMPLATE_NAME => 'test.html.twig',
        ]);

        $route->getTemplateName()->willReturn('test.html.twig');
        $route->getType()->shouldBeCalled();
        $article->getRoute()->willReturn($route);
        $article->getTemplateName()->willReturn(null);
        $this->setTemplateName($article, [RouteObjectInterface::ROUTE_OBJECT => $route])->shouldReturn([
            RouteObjectInterface::ROUTE_OBJECT => $route,
            RouteObjectInterface::TEMPLATE_NAME => 'test.html.twig',
        ]);

        $route->getTemplateName()->willReturn('test.html.twig');
        $article->getRoute()->willReturn($route);
        $article->getTemplateName()->willReturn('article2.html.twig');
        $this->setTemplateName($article, [])->shouldReturn([
            RouteObjectInterface::TEMPLATE_NAME => 'article2.html.twig',
        ]);
    }
}
