<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use DateTime;
use Doctrine\Common\Collections\Collection;

trait MediaAwareTrait
{
    protected $media;

    protected $featureMedia;

    protected $mediaUpdatedAt;

    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function setMedia(Collection $media): void
    {
        $this->media = $media;
    }

    public function getFeatureMedia(): ?ArticleMediaInterface
    {
        return $this->featureMedia;
    }

    public function setFeatureMedia(ArticleMediaInterface $featureMedia = null): void
    {
        $this->featureMedia = $featureMedia;
    }

    public function getMediaUpdatedAt(): ?DateTime
    {
        return $this->mediaUpdatedAt;
    }

    public function setMediaUpdatedAt(DateTime $mediaUpdatedAt): void
    {
        $this->mediaUpdatedAt = $mediaUpdatedAt;
    }

    public function hasArticleMedia(ArticleMediaInterface $articleMedia): bool
    {
        return $this->media->contains($articleMedia);
    }

    public function removeEmbeddedImages(): void
    {
        /** @var ArticleMediaInterface[] $embeddedImagesMedia */
        $embeddedImagesMedia = $this->media->filter(static function ($media) {
            /* @var ArticleMediaInterface $media */
            return ArticleMediaInterface::TYPE_EMBEDDED_IMAGE === $media->getMediaType();
        });

        foreach ($embeddedImagesMedia as $embeddedImageMedia) {
            if ($this->hasArticleMedia($embeddedImageMedia)) {
                $this->media->removeElement($embeddedImageMedia);
            }
        }
    }
}
