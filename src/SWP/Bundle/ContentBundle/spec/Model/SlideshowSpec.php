<?php
declare(strict_types=1);

namespace spec\SWP\Bundle\ContentBundle\Model;

use SWP\Bundle\ContentBundle\Model\Slideshow;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SWP\Bundle\ContentBundle\Model\SlideshowInterface;

final class SlideshowSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Slideshow::class);
    }

    public function it_should_implement_article_interface()
    {
        $this->shouldImplement(SlideshowInterface::class);
    }
}
