<?php

namespace spec\SWP\Bundle\FacebookInstantArticlesBundle\Manager;

use Facebook\Facebook;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\FacebookInstantArticlesBundle\Model\ApplicationInterface;

class FacebookManagerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\Bundle\FacebookInstantArticlesBundle\Manager\FacebookManager');
    }

    public function it_should_create_facebook_instance(ApplicationInterface $application)
    {
        $application->getAppId()->willReturn('3245324642364564262345');
        $application->getAppSecret()->willReturn(Argument::type('string'));

        $this->createForApp($application)->shouldReturnAnInstanceOf(Facebook::class);
    }
}
