<?php

namespace SWP\Component\Seo\Model;

trait SeoMetadataAwareTrait
{
    /**
     * @var SeoMetadataInterface|null
     */
    protected $seoMetadata;

    public function getSeoMetadata(): ?SeoMetadataInterface
    {
        return $this->seoMetadata;
    }

    public function setSeoMetadata(?SeoMetadataInterface $seoMetadata): void
    {
        $this->seoMetadata = $seoMetadata;
    }
}
