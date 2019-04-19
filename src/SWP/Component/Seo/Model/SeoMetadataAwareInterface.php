<?php

declare(strict_types=1);

namespace SWP\Component\Seo\Model;

interface SeoMetadataAwareInterface
{
    public function getSeoMetadata(): ?SeoMetadataInterface;

    public function setSeoMetadata(?SeoMetadataInterface $seoMetadata): void;
}
