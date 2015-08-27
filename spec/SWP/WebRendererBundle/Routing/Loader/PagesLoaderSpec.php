<?php

namespace spec\SWP\WebRendererBundle\Routing\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\WebRendererBundle\Entity\Page;
use Symfony\Component\Routing\RouteCollection;

class PagesLoaderSpec extends ObjectBehavior
{
    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Doctrine\ORM\AbstractQuery $query
     */
    function let($em, $query)
    {
        $this->beConstructedWith($em);
        $em->createQuery(Argument::any('string'))->willReturn($query);
        $query->execute()->willReturn([]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('SWP\WebRendererBundle\Routing\Loader\PagesLoader');
    }

    function it_should_work_without_defined_routes()
    {
        $this->load('.', 'pages')->shouldReturnAnInstanceOf('\Symfony\Component\Routing\RouteCollection');
    }

    function it_should_load_routes($query)
    {
        $query->execute()->willReturn([
            new Page(), new Page()
        ]);
        $this->load('.', 'pages')->shouldReturnAnInstanceOf('\Symfony\Component\Routing\RouteCollection');
    }

    function it_should_support_type()
    {
        $this->supports('.', 'pages')->shouldReturn(true);
    }
}
