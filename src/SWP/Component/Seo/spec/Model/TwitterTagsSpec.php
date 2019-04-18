<?php

namespace spec\SWP\Component\Seo\Model;

use SWP\Component\Seo\Model\Metadata;
use SWP\Component\Seo\Model\SeoMetadataInterface;
use SWP\Component\Seo\Model\TwitterTags;
use PhpSpec\ObjectBehavior;

class TwitterTagsSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TwitterTags::class);
        $this->shouldHaveType(Metadata::class);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(SeoMetadataInterface::class);
    }
}
