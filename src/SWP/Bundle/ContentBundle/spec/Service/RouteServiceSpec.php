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
namespace spec\SWP\Bundle\ContentBundle\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\RouteObjectInterface;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Factory\RouteFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Bundle\ContentBundle\RouteEvents;
use SWP\Bundle\ContentBundle\Service\RouteService;
use SWP\Bundle\ContentBundle\Service\RouteServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin RouteService
 */
class RouteServiceSpec extends ObjectBehavior
{
    public function let(
        RouteFactoryInterface $routeFactory,
        RouteProviderInterface $routeProvider,
        ArticleProviderInterface $articleProvider,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($routeFactory, $routeProvider, $articleProvider, $eventDispatcher);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RouteService::class);
    }

    public function it_implements_route_service_interface()
    {
        $this->shouldImplement(RouteServiceInterface::class);
    }

    public function it_creates_a_new_content_route(
        RouteFactoryInterface $routeFactory,
        RouteObjectInterface $route,
        RouteObjectInterface $parentRoute,
        EventDispatcherInterface $eventDispatcher
    ) {
        $routeFactory->create()->willReturn($route);

        $eventDispatcher->dispatch(
            RouteEvents::PRE_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $route->setVariablePattern(null)->shouldBeCalled();
        $route->setRequirements([])->shouldBeCalled();
        $route->setName('test-name')->shouldBeCalled();
        $route->setType(RouteObjectInterface::TYPE_CONTENT)->shouldBeCalled();
        $route->setTemplateName('index.html.twig')->shouldBeCalled();
        $route->setParentDocument($parentRoute)->shouldNotBeCalled();

        $eventDispatcher->dispatch(
            RouteEvents::POST_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $this->createRoute([
            'name' => 'test-name',
            'template' => 'article.html.twig',
            'type' => RouteObjectInterface::TYPE_CONTENT,
            'template_name' => 'index.html.twig',
        ])->shouldReturn($route);
    }

    public function it_creates_a_new_collection_route(
        RouteFactoryInterface $routeFactory,
        RouteObjectInterface $route,
        EventDispatcherInterface $eventDispatcher
    ) {
        $routeFactory->create()->willReturn($route);

        $eventDispatcher->dispatch(
            RouteEvents::PRE_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $route->setVariablePattern(Argument::exact('/{slug}'))->shouldBeCalled();
        $route->setRequirement(Argument::exact('slug'), Argument::exact('[a-zA-Z1-9\-_\/]+'))->shouldBeCalled();
        $route->setName('test-name')->shouldBeCalled();
        $route->setType(RouteObjectInterface::TYPE_COLLECTION)->shouldBeCalled();
        $route->setTemplateName('index.html.twig')->shouldNotBeCalled();

        $eventDispatcher->dispatch(
            RouteEvents::POST_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $this->createRoute([
            'name' => 'test-name',
            'type' => RouteObjectInterface::TYPE_COLLECTION,
        ])->shouldReturn($route);
    }

    public function it_creates_a_new_collection_route_with_parent_and_content(
        RouteFactoryInterface $routeFactory,
        RouteObjectInterface $route,
        RouteObjectInterface $parentRoute,
        ArticleInterface $article,
        EventDispatcherInterface $eventDispatcher,
        RouteProviderInterface $routeProvider,
        ArticleProviderInterface $articleProvider
    ) {
        $routeFactory->create()->willReturn($route);

        $eventDispatcher->dispatch(
            RouteEvents::PRE_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $route->setVariablePattern(Argument::exact('/{slug}'))->shouldBeCalled();
        $route->setRequirement(Argument::exact('slug'), Argument::exact('[a-zA-Z1-9\-_\/]+'))->shouldBeCalled();
        $route->setName('test-name')->shouldBeCalled();
        $route->setType(RouteObjectInterface::TYPE_COLLECTION)->shouldBeCalled();
        $route->setTemplateName('index.html.twig')->shouldNotBeCalled();
        $route->setContent($article)->shouldBeCalled();
        $route->setParentDocument($parentRoute)->shouldBeCalled();

        $articleProvider->getOneById('content-object')->willReturn($article);
        $routeProvider->getOneById('parent-route')->willReturn($parentRoute);

        $eventDispatcher->dispatch(
            RouteEvents::POST_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $this->createRoute([
            'name' => 'test-name',
            'type' => RouteObjectInterface::TYPE_COLLECTION,
            'content' => 'content-object',
            'parent' => 'parent-route',
        ])->shouldReturn($route);
    }

    public function it_creates_a_new_collection_route_when_content_not_found(
        RouteFactoryInterface $routeFactory,
        RouteObjectInterface $route,
        RouteObjectInterface $parentRoute,
        ArticleInterface $article,
        EventDispatcherInterface $eventDispatcher,
        RouteProviderInterface $routeProvider,
        ArticleProviderInterface $articleProvider
    ) {
        $routeFactory->create()->willReturn($route);

        $eventDispatcher->dispatch(
            RouteEvents::PRE_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $route->setVariablePattern(Argument::exact('/{slug}'))->shouldBeCalled();
        $route->setRequirement(Argument::exact('slug'), Argument::exact('[a-zA-Z1-9\-_\/]+'))->shouldBeCalled();
        $route->setName('test-name')->shouldBeCalled();
        $route->setType(RouteObjectInterface::TYPE_COLLECTION)->shouldBeCalled();
        $route->setTemplateName('index.html.twig')->shouldNotBeCalled();
        $route->setContent($article)->shouldNotBeCalled();
        $route->setParentDocument($parentRoute)->shouldBeCalled();

        $articleProvider->getOneById('content-object')->willReturn(null);
        $routeProvider->getOneById('parent-route')->willReturn($parentRoute);

        $eventDispatcher->dispatch(
            RouteEvents::POST_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $this->createRoute([
            'name' => 'test-name',
            'type' => RouteObjectInterface::TYPE_COLLECTION,
            'content' => 'content-object',
            'parent' => 'parent-route',
        ])->shouldReturn($route);
    }

    public function it_creates_a_new_collection_route_when_root_parent_set(
        RouteFactoryInterface $routeFactory,
        RouteObjectInterface $route,
        RouteObjectInterface $parentRoute,
        ArticleInterface $article,
        EventDispatcherInterface $eventDispatcher,
        RouteProviderInterface $routeProvider,
        ArticleProviderInterface $articleProvider
    ) {
        $routeFactory->create()->willReturn($route);

        $eventDispatcher->dispatch(
            RouteEvents::PRE_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $route->setVariablePattern(Argument::exact('/{slug}'))->shouldBeCalled();
        $route->setRequirement(Argument::exact('slug'), Argument::exact('[a-zA-Z1-9\-_\/]+'))->shouldBeCalled();
        $route->setName('test-name')->shouldBeCalled();
        $route->setType(RouteObjectInterface::TYPE_COLLECTION)->shouldBeCalled();
        $route->setTemplateName('index.html.twig')->shouldNotBeCalled();
        $route->setContent($article)->shouldBeCalled();
        $route->setParentDocument($parentRoute)->shouldBeCalled();

        $articleProvider->getOneById('content-object')->willReturn($article);
        $routeProvider->getOneById('parent-route')->willReturn(null);
        $routeProvider->getBaseRoute()->willReturn($parentRoute);

        $eventDispatcher->dispatch(
            RouteEvents::POST_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $this->createRoute([
            'name' => 'test-name',
            'type' => RouteObjectInterface::TYPE_COLLECTION,
            'content' => 'content-object',
            'parent' => '/',
        ])->shouldReturn($route);
    }

    public function it_creates_a_new_collection_route_when_parent_not_found(
        RouteFactoryInterface $routeFactory,
        RouteObjectInterface $route,
        RouteObjectInterface $parentRoute,
        ArticleInterface $article,
        EventDispatcherInterface $eventDispatcher,
        RouteProviderInterface $routeProvider,
        ArticleProviderInterface $articleProvider
    ) {
        $routeFactory->create()->willReturn($route);

        $eventDispatcher->dispatch(
            RouteEvents::PRE_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $route->setVariablePattern(Argument::exact('/{slug}'))->shouldBeCalled();
        $route->setRequirement(Argument::exact('slug'), Argument::exact('[a-zA-Z1-9\-_\/]+'))->shouldBeCalled();
        $route->setName('test-name')->shouldBeCalled();
        $route->setType(RouteObjectInterface::TYPE_COLLECTION)->shouldBeCalled();
        $route->setTemplateName('index.html.twig')->shouldNotBeCalled();
        $route->setContent($article)->shouldBeCalled();
        $route->setParentDocument($parentRoute)->shouldNotBeCalled();

        $articleProvider->getOneById('content-object')->willReturn($article);
        $routeProvider->getOneById('parent-route')->willReturn(null);

        $eventDispatcher->dispatch(
            RouteEvents::POST_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $this->createRoute([
            'name' => 'test-name',
            'type' => RouteObjectInterface::TYPE_COLLECTION,
            'content' => 'content-object',
            'parent' => 'parent-route',
        ])->shouldReturn($route);
    }

    public function it_should_update_existing_route_name(RouteObjectInterface $route, EventDispatcherInterface $eventDispatcher)
    {
        $route->getName()->willReturn('name');

        $eventDispatcher->dispatch(
            RouteEvents::PRE_UPDATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $route->setName('edited name')->shouldBeCalled();

        $eventDispatcher->dispatch(
            RouteEvents::POST_UPDATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $this->updateRoute($route, ['name' => 'edited name'])->shouldReturn($route);
    }
}
