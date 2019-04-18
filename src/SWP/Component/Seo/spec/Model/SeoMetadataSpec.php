<?php

declare(strict_types=1);

namespace spec\SWP\Component\Seo\Model;

use SWP\Component\Seo\Model\Metadata;
use SWP\Component\Seo\Model\SeoMetadata;
use PhpSpec\ObjectBehavior;
use SWP\Component\Seo\Model\SeoMetadataInterface;

class SeoMetadataSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SeoMetadata::class);
        $this->shouldHaveType(Metadata::class);
    }

    public function it_implements_interface(): void
    {
        $this->shouldImplement(SeoMetadataInterface::class);
    }
}
