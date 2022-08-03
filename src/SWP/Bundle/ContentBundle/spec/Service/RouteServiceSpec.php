<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\ContentBundle\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\Model\Route;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
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
        EventDispatcherInterface $eventDispatcher,
        RouteRepositoryInterface $routeRepository
    ) {
        $this->beConstructedWith($eventDispatcher, $routeRepository);
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
        Route $route,
        EventDispatcherInterface $eventDispatcher,
        RouteInterface $parent
    ) {
        $route->getType()->willReturn(RouteInterface::TYPE_CONTENT);
        $route->getName()->willReturn('test-name');
        $route->getSlug()->willReturn('test-name');
        $route->getTemplateName()->willReturn('index.html.twig');
        $route->getParent()->willReturn($parent);

        $eventDispatcher->dispatch(
            Argument::type(RouteEvent::class),
            RouteEvents::PRE_CREATE
        )->shouldBeCalled();

        $route->setVariablePattern(null)->shouldBeCalled();
        $route->setRequirements([])->shouldBeCalled();
        $route->setStaticPrefix('/test-name')->shouldBeCalled();

        $eventDispatcher->dispatch(
            Argument::type(RouteEvent::class),
            RouteEvents::POST_CREATE,
        )->shouldBeCalled();

        $this->createRoute($route)->shouldReturn($route);
    }

    public function it_creates_a_new_content_route_with_custom_slug(
        Route $route,
        EventDispatcherInterface $eventDispatcher,
        RouteInterface $parent
    ) {
        $route->getType()->willReturn(RouteInterface::TYPE_CONTENT);
        $route->getName()->willReturn('Test Name');
        $route->getSlug()->willReturn('test-name-2');
        $route->getTemplateName()->willReturn('index.html.twig');
        $route->getParent()->willReturn($parent);

        $eventDispatcher->dispatch(
            Argument::type(RouteEvent::class),
            RouteEvents::PRE_CREATE
        )->shouldBeCalled();

        $route->setVariablePattern(null)->shouldBeCalled();
        $route->setRequirements([])->shouldBeCalled();
        $route->setStaticPrefix('/test-name-2')->shouldBeCalled();

        $eventDispatcher->dispatch(
            Argument::type(RouteEvent::class),
            RouteEvents::POST_CREATE
        )->shouldBeCalled();

        $this->createRoute($route)->shouldReturn($route);
    }

    public function it_creates_a_new_content_route_with_slug_created_from_name(
        Route $route,
        EventDispatcherInterface $eventDispatcher,
        RouteInterface $parent
    ) {
        $route->getType()->willReturn(RouteInterface::TYPE_CONTENT);
        $route->getName()->willReturn('Test Name');
        $route->getSlug()->willReturn('test-name');
        $route->getTemplateName()->willReturn('index.html.twig');
        $route->getParent()->willReturn($parent);

        $eventDispatcher->dispatch(
            Argument::type(RouteEvent::class),
            RouteEvents::PRE_CREATE
        )->shouldBeCalled();

        $route->setVariablePattern(null)->shouldBeCalled();
        $route->setRequirements([])->shouldBeCalled();
        $route->setStaticPrefix('/test-name')->shouldBeCalled();

        $eventDispatcher->dispatch(
            Argument::type(RouteEvent::class),
            RouteEvents::POST_CREATE
        )->shouldBeCalled();

        $this->createRoute($route)->shouldReturn($route);
    }

    public function it_creates_a_new_collection_route(
        Route $route,
        EventDispatcherInterface $eventDispatcher,
        RouteInterface $parent
    ) {
        $route->getType()->willReturn(RouteInterface::TYPE_COLLECTION);
        $route->getName()->willReturn('test-name');
        $route->getSlug()->willReturn('test-name');
        $route->getTemplateName()->willReturn('index.html.twig');
        $route->getParent()->willReturn($parent);

        $eventDispatcher->dispatch(
            Argument::type(RouteEvent::class),
            RouteEvents::PRE_CREATE
        )->shouldBeCalled();

        $route->setVariablePattern(Argument::exact('/{slug}'))->shouldBeCalled();
        $route->setRequirement(Argument::exact('slug'), Argument::exact('[a-zA-Z0-9*\-_]+'))->shouldBeCalled();
        $route->setDefault('slug', null)->shouldBeCalled();
        $route->setStaticPrefix('/test-name')->shouldBeCalled();

        $eventDispatcher->dispatch(
            Argument::type(RouteEvent::class),
            RouteEvents::POST_CREATE
        )->shouldBeCalled();

        $this->createRoute($route)->shouldReturn($route);
    }

    public function it_should_update_existing_route(
        RouteInterface $route,
        EventDispatcherInterface $eventDispatcher,
        RouteInterface $parent
    ) {
        $route->getType()->willReturn(RouteInterface::TYPE_COLLECTION);
        $route->getParent()->willReturn($parent);
        $route->getName()->willReturn('test-name');
        $route->getSlug()->willReturn('test-name');

        $eventDispatcher->dispatch(
            Argument::type(RouteEvent::class),
            RouteEvents::PRE_UPDATE
        )->shouldBeCalled();

        $route->setVariablePattern(Argument::exact('/{slug}'))->shouldBeCalled();
        $route->setRequirement(Argument::exact('slug'), Argument::exact('[a-zA-Z0-9*\-_]+'))->shouldBeCalled();
        $route->setDefault('slug', null)->shouldBeCalled();
        $route->setStaticPrefix('/test-name')->shouldBeCalled();

        $eventDispatcher->dispatch(
            Argument::type(RouteEvent::class),
            RouteEvents::POST_UPDATE
        )->shouldBeCalled();

        $this->updateRoute($route, $route)->shouldReturn($route);
    }
}
