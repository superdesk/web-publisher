<?php

namespace spec\SWP\Bundle\CoreBundle\Serializer;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Serializer\TenantHandler;

final class TenantHandlerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(TenantHandler::class);
    }
}
