<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\Bundle\ContentBundle\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\RouteObjectInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProvider;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

/**
 * @mixin RouteProvider
 */
class RouteProviderSpec extends ObjectBehavior
{
    public function let(
        RepositoryInterface $routeRepository,
        TenantAwarePathBuilderInterface $pathBuilder
    ) {
        $this->beConstructedWith($routeRepository, $pathBuilder, ['basepath1', 'basepath2'], 'defaultpath');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RouteProvider::class);
    }

    public function it_implements_route_provider_interface()
    {
        $this->shouldImplement(RouteProviderInterface::class);
    }

    public function it_gets_base_route(
        RouteObjectInterface $route,
        TenantAwarePathBuilderInterface $pathBuilder,
        RepositoryInterface $routeRepository
    ) {
        $pathBuilder->build(Argument::exact('basepath1'))->willReturn('/route/path/basepath1');
        $routeRepository->find('/route/path/basepath1')->willReturn($route);

        $this->getBaseRoute()->shouldReturn($route);
    }

    public function it_should_not_get_base_route(
        TenantAwarePathBuilderInterface $pathBuilder,
        RepositoryInterface $routeRepository
    ) {
        $pathBuilder->build(Argument::exact('basepath1'))->willReturn('/route/path/basepath1');
        $routeRepository->find('/route/path/basepath1')->willReturn(null);

        $this->getBaseRoute()->shouldBeNull();
    }

    public function it_gets_one_route_by_id(
        RouteObjectInterface $route,
        TenantAwarePathBuilderInterface $pathBuilder,
        RepositoryInterface $routeRepository
    ) {
        $pathBuilder->build(Argument::exact('basepath1/id'))->willReturn('/route/path/basepath1/id');
        $routeRepository->find('/route/path/basepath1/id')->willReturn($route);

        $this->getOneById('id')->shouldReturn($route);
    }

    public function it_should_return_null_when_getting_route_by_id(
        TenantAwarePathBuilderInterface $pathBuilder,
        RepositoryInterface $routeRepository
    ) {
        $pathBuilder->build(Argument::exact('basepath1/id'))->willReturn('/route/path/basepath1/id');
        $routeRepository->find('/route/path/basepath1/id')->willReturn(null);

        $this->getOneById('id')->shouldBeNull();
    }

    public function it_should_get_route_for_article(
        ArticleInterface $article,
        RouteObjectInterface $route,
        TenantAwarePathBuilderInterface $pathBuilder,
        RepositoryInterface $routeRepository
    ) {
        $pathBuilder->build(Argument::exact('basepath1/defaultpath'))->willReturn('/route/path/basepath1/defaultpath');
        $routeRepository->find('/route/path/basepath1/defaultpath')->willReturn($route);

        $this->getRouteForArticle($article)->shouldReturn($route);
    }

    public function it_should_not_get_route_for_article(
        ArticleInterface $article,
        TenantAwarePathBuilderInterface $pathBuilder,
        RepositoryInterface $routeRepository
    ) {
        $pathBuilder->build(Argument::exact('basepath1/defaultpath'))->willReturn('/route/path/basepath1/defaultpath');
        $routeRepository->find('/route/path/basepath1/defaultpath')->willReturn(null);

        $this->getRouteForArticle($article)->shouldBe(null);
    }
}
