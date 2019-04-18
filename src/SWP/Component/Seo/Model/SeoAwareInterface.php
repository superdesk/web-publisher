<?php

declare(strict_types=1);

namespace SWP\Component\Seo\Model;

interface SeoAwareInterface
{
    public function getOgTags(): SeoMetadataInterface;

    public function setOgTags(SeoMetadataInterface $ogTags): void;

    public function getTwitterTags(): SeoMetadataInterface;

    public function setTwitterTags(SeoMetadataInterface $twitterTags): void;

    public function getSeoMetadata(): SeoMetadataInterface;

    public function setSeoMetadata(SeoMetadataInterface $seoMetadata): void;
}
