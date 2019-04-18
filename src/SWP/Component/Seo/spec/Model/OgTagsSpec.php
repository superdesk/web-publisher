<?php

namespace spec\SWP\Component\Seo\Model;

use SWP\Component\Seo\Model\Metadata;
use SWP\Component\Seo\Model\OgTags;
use PhpSpec\ObjectBehavior;
use SWP\Component\Seo\Model\SeoMetadataInterface;

class OgTagsSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(OgTags::class);
        $this->shouldHaveType(Metadata::class);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(SeoMetadataInterface::class);
    }
}
