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

class ImageRendition implements ImageRenditionInterface
{
    use PreviewUrlAwareTrait;

    protected $width;

    protected $height;

    protected $name;

    protected $id;

    protected $image;

    protected $media;

    protected $convertedToWebp;

    public function getMedia(): ArticleMediaInterface
    {
        return $this->media;
    }

    public function setMedia(ArticleMediaInterface $media): void
    {
        $this->media = $media;
    }

    public function getImage(): ImageInterface
    {
        return $this->image;
    }

    public function setImage(ImageInterface $image): void
    {
        $this->image = $image;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id)
    {
        $this->id = $id;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function isConvertedToWebp(): bool
    {
        return $this->getImage()->hasVariant(ImageInterface::VARIANT_WEBP);
    }
}
