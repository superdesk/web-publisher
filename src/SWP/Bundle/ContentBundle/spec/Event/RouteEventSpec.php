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
namespace spec\SWP\Bundle\ContentBundle\Event;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @mixin RouteEvent
 */
class RouteEventSpec extends ObjectBehavior
{
    public function let(RouteInterface $route)
    {
        $this->beConstructedWith($route);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RouteEvent::class);
        $this->shouldHaveType(Event::class);
    }

    public function it_has_a_route(RouteInterface $route)
    {
        $this->getRoute()->shouldReturn($route);
    }
}
