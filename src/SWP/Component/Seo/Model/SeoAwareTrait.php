<?php

namespace SWP\Component\Seo\Model;

trait SeoAwareTrait
{
    /**
     * @var SeoMetadataInterface
     */
    protected $ogTags;

    /**
     * @var SeoMetadataInterface
     */
    protected $twitterTags;

    /**
     * @var SeoMetadataInterface
     */
    protected $seoMetadata;

    public function getOgTags(): SeoMetadataInterface
    {
        return $this->ogTags;
    }

    public function setOgTags(SeoMetadataInterface $ogTags): void
    {
        $this->ogTags = $ogTags;
    }

    public function getTwitterTags(): SeoMetadataInterface
    {
        return $this->twitterTags;
    }

    public function setTwitterTags(SeoMetadataInterface $twitterTags): void
    {
        $this->twitterTags = $twitterTags;
    }

    public function getSeoMetadata(): SeoMetadataInterface
    {
        return $this->seoMetadata;
    }

    public function setSeoMetadata(SeoMetadataInterface $seoMetadata): void
    {
        $this->seoMetadata = $seoMetadata;
    }
}
