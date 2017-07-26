<?php

declare(strict_types=1);

namespace spec\SWP\Bundle\ContentBundle\Model;

use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Model\ArticleSource;
use SWP\Bundle\ContentBundle\Model\ArticleSourceInterface;

class ArticleSourceSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ArticleSource::class);
    }

    public function it_should_implement_article_interface()
    {
        $this->shouldImplement(ArticleSourceInterface::class);
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable()
    {
        $this->setName('FOX News');
        $this->getName()->shouldReturn('FOX News');
    }
}
