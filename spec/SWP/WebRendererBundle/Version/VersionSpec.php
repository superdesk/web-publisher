<?php

namespace spec\SWP\WebRendererBundle\Version;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class VersionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('SWP\WebRendererBundle\Version\Version');
        $this->shouldImplement('SWP\UpdaterBundle\Version\VersionInterface');
    }

    function it_should_return_version()
    {
        $this->setVersion('1.1.1')->shouldReturn($this);
        $this->getVersion()->shouldReturn('1.1.1');
    }

}
