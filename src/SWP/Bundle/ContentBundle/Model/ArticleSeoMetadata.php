<?php

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Seo\Model\SeoMetadata;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleSeoMetadata extends SeoMetadata implements ArticleSeoMetadataInterface
{
    /**
     * @var UploadedFile|null
     */
    protected $metaMediaFile;

    /**
     * @var UploadedFile|null
     */
    protected $ogMediaFile;

    /**
     * @var UploadedFile|null
     */
    protected $twitterMediaFile;

    /**
     * @return UploadedFile|null
     */
    public function getMetaMediaFile(): ?UploadedFile
    {
        return $this->metaMediaFile;
    }

    /**
     * @param UploadedFile|null $metaMediaFile
     */
    public function setMetaMediaFile(?UploadedFile $metaMediaFile): void
    {
        $this->metaMediaFile = $metaMediaFile;
    }

    /**
     * @return UploadedFile|null
     */
    public function getOgMediaFile(): ?UploadedFile
    {
        return $this->ogMediaFile;
    }

    /**
     * @param UploadedFile|null $ogMediaFile
     */
    public function setOgMediaFile(?UploadedFile $ogMediaFile): void
    {
        $this->ogMediaFile = $ogMediaFile;
    }

    /**
     * @return UploadedFile|null
     */
    public function getTwitterMediaFile(): ?UploadedFile
    {
        return $this->twitterMediaFile;
    }

    /**
     * @param UploadedFile|null $twitterMediaFile
     */
    public function setTwitterMediaFile(?UploadedFile $twitterMediaFile): void
    {
        $this->twitterMediaFile = $twitterMediaFile;
    }
}
