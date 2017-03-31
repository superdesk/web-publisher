<?php

declare(strict_types=1);

namespace spec\SWP\Bundle\CoreBundle\Service;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\CoreBundle\Service\ArticlePublisher;
use SWP\Bundle\CoreBundle\Service\ArticlePublisherInterface;

final class ArticlePublisherSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticlePublisher::class);
    }

    public function it_implements_interface()
    {
        $this->shouldImplement(ArticlePublisherInterface::class);
    }
}
