<?php

namespace spec\SWP\Bundle\ContentBundle\Factory;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Factory\ArticleFactory;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * @mixin ArticleFactory
 */
class ArticleFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, RouteProviderInterface $routeProvider, ArticleProviderInterface $articleProvider)
    {
        $this->beConstructedWith($factory, $routeProvider, $articleProvider, 'test');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ArticleFactory::class);
    }
}
