<?php

namespace spec\SWP\Component\Common\Response;

use PhpSpec\ObjectBehavior;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\ResponseContextInterface;

class ResponseContextSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ResponseContext::class);
    }

    public function it_should_implement_interface()
    {
        $this->shouldImplement(ResponseContextInterface::class);
    }

    public function it_should_set_headers()
    {
        $this->beConstructedWith(200, ResponseContextInterface::INTENTION_API, ['header1' => 'value 1']);
        $this->getHeaders()->shouldBeArray();
        $this->getHeaders()->shouldReturn(['header1' => 'value 1']);
    }

    public function it_should_store_cleared_cookies()
    {
        $this->clearCookie('session');
        $this->getClearedCookies()->shouldBeArray();
        $this->getClearedCookies()->shouldReturn(['session']);
    }
}
