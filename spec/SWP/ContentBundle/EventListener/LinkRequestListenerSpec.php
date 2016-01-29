<?php

namespace spec\SWP\ContentBundle\EventListener;

use PhpSpec\ObjectBehavior;

class LinkRequestListenerSpec extends ObjectBehavior
{
    /**
     * @param \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface $controllerResolver
     * @param \Symfony\Component\Routing\Matcher\UrlMatcherInterface               $urlMatcher
     */
    public function let($controllerResolver, $urlMatcher)
    {
        $this->beConstructedWith($controllerResolver, $urlMatcher);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\ContentBundle\EventListener\LinkRequestListener');
    }
}
