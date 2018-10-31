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

use SWP\Component\Storage\Model\PersistableInterface;

/**
 * ImageRendition represents media which belongs to Article.
 */
class ImageRendition implements ImageRenditionInterface, PersistableInterface
{
    /**
     * @var string
     */
    protected $width;

    /**
     * @var string
     */
    protected $height;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var ImageInterface
     */
    protected $image;

    /**
     * @var ArticleMediaInterface
     */
    protected $media;

    /**
     * @var null|string
     */
    protected $previewUrl;

    public function setPreviewUrl(?string $previewUrl): void
    {
        $this->previewUrl = $previewUrl;
    }

    public function getPreviewUrl(): ?string
    {
        return $this->previewUrl;
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
     * @return ImageInterface
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param ImageInterface $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param null|int $id
     */
    public function setId(?int $id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     *
     * @return ImageRendition
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $height
     *
     * @return ImageRendition
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return ImageRendition
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
