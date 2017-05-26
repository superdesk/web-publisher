<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Model;

use SWP\Component\Common\Model\SoftDeletableTrait;

class Rendition implements RenditionInterface
{
    use SoftDeletableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $href;

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
    protected $mimetype;

    /**
     * @var string
     */
    protected $media;

    /**
     * @var ItemInterface
     */
    protected $item;

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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * {@inheritdoc}
     */
    public function setHref(string $href)
    {
        $this->href = $href;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * {@inheritdoc}
     */
    public function setWidth(int $width)
    {
        $this->width = $width;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeight(int $height)
    {
        $this->height = $height;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    /**
     * {@inheritdoc}
     */
    public function setMimetype(string $mimetype)
    {
        $this->mimetype = $mimetype;
    }

    /**
     * {@inheritdoc}
     */
    public function getMedia(): string
    {
        return $this->media;
    }

    /**
     * {@inheritdoc}
     */
    public function setMedia(string $media)
    {
        $this->media = $media;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem(): ItemInterface
    {
        return $this->item;
    }

    /**
     * {@inheritdoc}
     */
    public function setItem(ItemInterface $item)
    {
        $this->item = $item;
    }
}
