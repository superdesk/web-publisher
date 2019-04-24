<?php

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Seo\Model\SeoMetadata;

class ArticleSeoMetadata extends SeoMetadata implements ArticleSeoMetadataInterface
{
    /**
     * @var ImageInterface|null
     */
    protected $metaImage;

    /**
     * @var ImageInterface|null
     */
    protected $ogImage;

    /**
     * @var ImageInterface|null
     */
    protected $twitterImage;

    public function getMetaImage(): ?ImageInterface
    {
        return $this->metaImage;
    }

    public function setMetaImage(?ImageInterface $metaImage): void
    {
        $this->metaImage = $metaImage;
    }

    public function getOgImage(): ?ImageInterface
    {
        return $this->ogImage;
    }

    public function setOgImage(?ImageInterface $ogImage): void
    {
        $this->ogImage = $ogImage;
    }

    public function getTwitterImage(): ?ImageInterface
    {
        return $this->twitterImage;
    }

    public function setTwitterImage(?ImageInterface $twitterImage): void
    {
        $this->twitterImage = $twitterImage;
    }
}
