<?php

namespace spec\SWP\WebRendererBundle\Routing\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\ContentBundle\Model\Page;

class PagesLoaderSpec extends ObjectBehavior
{
    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $managerRegistry
     * @param \Doctrine\ORM\EntityManager              $em
     * @param \Doctrine\ORM\AbstractQuery              $query
     */
    public function let($managerRegistry, $em, $query)
    {
        $this->beConstructedWith($managerRegistry);
        $managerRegistry->getManager()->willReturn($em);
        $em->createQuery(Argument::any('string'))->willReturn($query);
        $query->execute()->willReturn([]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\WebRendererBundle\Routing\Loader\PagesLoader');
    }

    public function it_should_work_without_defined_routes()
    {
        $this->load('.', 'pages')->shouldReturnAnInstanceOf('\Symfony\Component\Routing\RouteCollection');
    }

    public function it_should_load_routes($query)
    {
        $query->execute()->willReturn([
            new Page(), new Page(),
        ]);
        $this->load('.', 'pages')->shouldReturnAnInstanceOf('\Symfony\Component\Routing\RouteCollection');
    }

    public function it_should_support_type()
    {
        $this->supports('.', 'pages')->shouldReturn(true);
    }
}
