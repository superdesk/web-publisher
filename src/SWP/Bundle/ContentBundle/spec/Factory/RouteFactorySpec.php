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
namespace spec\SWP\Bundle\ContentBundle\Factory;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Factory\RouteFactory;
use SWP\Bundle\ContentBundle\Factory\RouteFactoryInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * @mixin RouteFactory
 */
class RouteFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RouteFactory::class);
    }

    public function it_has_route_factory_interface()
    {
        $this->shouldImplement(RouteFactoryInterface::class);
    }

    public function it_creates_new_route_object(FactoryInterface $factory, RouteInterface $route)
    {
        $factory->create()->willReturn($route);

        $this->create()->shouldReturn($route);
    }
}
