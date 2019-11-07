<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use function in_array;
use SWP\Component\Common\Model\TimestampableTrait;

class Image implements ImageInterface
{
    use TimestampableTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * Uploaded file extension.
     *
     * @var string
     */
    protected $fileExtension;

    /**
     * @var string
     */
    protected $assetId;

    /**
     * @var ArticleMediaInterface
     */
    protected $media;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var string
     */
    protected $length;

    /**
     * @var ArrayCollection
     */
    protected $renditions;

    protected $variants = [];

    /**
     * File constructor.
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setRenditions(new ArrayCollection());
    }

    /**
     * @return ArticleMediaInterface
     */
    public function getMedia(): ArticleMediaInterface
    {
        return $this->media;
    }

    /**
     * @param ArticleMediaInterface $media
     */
    public function setMedia(ArticleMediaInterface $media)
    {
        $this->media = $media;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileExtension($extension)
    {
        $this->fileExtension = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * @return string
     */
    public function getAssetId(): string
    {
        return $this->assetId;
    }

    /**
     * {@inheritdoc}
     */
    public function setAssetId(string $assetId)
    {
        $this->assetId = $assetId;
    }

    /**
     * @return ArrayCollection
     */
    public function getRenditions()
    {
        return $this->renditions;
    }

    /**
     * @param ImageRendition $rendition
     */
    public function addRendition(ImageRendition $rendition)
    {
        $this->renditions->add($rendition);
    }

    /**
     * @param ArrayCollection $renditions
     */
    public function setRenditions($renditions)
    {
        $this->renditions = $renditions;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function getLength(): string
    {
        return $this->length;
    }

    public function setLength(string $length): void
    {
        $this->length = $length;
    }

    public function getVariants(): array
    {
        return $this->variants;
    }

    public function setVariants(array $variants): void
    {
        $this->variants = $variants;
    }

    public function addVariant(string $variant): void
    {
        if (!$this->hasVariant($variant)) {
            $this->variants[] = $variant;
        }
    }

    public function hasVariant(string $variant): bool
    {
        return in_array($variant, $this->getVariants(), true);
    }
}
