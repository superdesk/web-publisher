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
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\RouteObjectInterface;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
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
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($eventDispatcher);
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
        RouteInterface $route,
        EventDispatcherInterface $eventDispatcher,
        RouteInterface $parent
    ) {
        $route->getType()->willReturn(RouteInterface::TYPE_CONTENT);
        $route->getName()->willReturn('test-name');
        $route->getTemplateName()->willReturn('index.html.twig');
        $route->getParent()->willReturn($parent);

        $eventDispatcher->dispatch(
            RouteEvents::PRE_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $route->setVariablePattern(null)->shouldBeCalled();
        $route->setRequirements([])->shouldBeCalled();
        $route->setStaticPrefix('/test-name')->shouldBeCalled();

        $eventDispatcher->dispatch(
            RouteEvents::POST_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $this->createRoute($route)->shouldReturn($route);
    }

    public function it_creates_a_new_collection_route(
        RouteObjectInterface $route,
        EventDispatcherInterface $eventDispatcher,
        RouteInterface $parent
    ) {
        $route->getType()->willReturn(RouteInterface::TYPE_COLLECTION);
        $route->getName()->willReturn('test-name');
        $route->getTemplateName()->willReturn('index.html.twig');
        $route->getParent()->willReturn($parent);

        $eventDispatcher->dispatch(
            RouteEvents::PRE_CREATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $route->setVariablePattern(Argument::exact('/{slug}'))->shouldBeCalled();
        $route->setRequirement(Argument::exact('slug'), Argument::exact('[a-zA-Z0-9*\-_\/]+'))->shouldBeCalled();
        $route->setDefault('slug', null)->shouldBeCalled();
        $route->setStaticPrefix('/test-name')->shouldBeCalled();

        $eventDispatcher->dispatch(
            RouteEvents::POST_CREATE,
            Argument::type(RouteEvent::class)
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

        $eventDispatcher->dispatch(
            RouteEvents::PRE_UPDATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $route->setVariablePattern(Argument::exact('/{slug}'))->shouldBeCalled();
        $route->setRequirement(Argument::exact('slug'), Argument::exact('[a-zA-Z0-9*\-_\/]+'))->shouldBeCalled();
        $route->setDefault('slug', null)->shouldBeCalled();
        $route->setStaticPrefix('/test-name')->shouldBeCalled();

        $eventDispatcher->dispatch(
            RouteEvents::POST_UPDATE,
            Argument::type(RouteEvent::class)
        )->shouldBeCalled();

        $this->updateRoute($route)->shouldReturn($route);
    }
}
