<?php

namespace spec\SWP\WebRendererBundle\Version;

use PhpSpec\ObjectBehavior;

class VersionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\WebRendererBundle\Version\Version');
        $this->shouldImplement('SWP\UpdaterBundle\Version\VersionInterface');
    }

    public function it_should_return_version()
    {
        $this->setVersion('1.1.1')->shouldReturn($this);
        $this->getVersion()->shouldReturn('1.1.1');
    }
}
