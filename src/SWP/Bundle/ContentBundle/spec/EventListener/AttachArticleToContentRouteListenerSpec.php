<?php

namespace spec\SWP\Bundle\ContentBundle\EventListener;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;

class AttachArticleToContentRouteListenerSpec extends ObjectBehavior
{

    public function let(RouteRepositoryInterface $routeRepository)
    {
        $this->beConstructedWith($routeRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\ContentBundle\EventListener\AttachArticleToContentRouteListener');
    }
}
