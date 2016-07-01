<?php

namespace spec\SWP\Component\Storage\Factory;

use PhpSpec\ObjectBehavior;
use SWP\Component\Storage\Factory\Factory;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * @mixin Factory
 */
class FactorySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(\stdClass::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Factory::class);
    }

    function it_implements_factory_interface()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_creates_an_instance_of_new_object()
    {
        $this->create()->shouldBeAnInstanceOf(\stdClass::class);
    }
}
