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

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\Image as BaseImage;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Component\Storage\Model\PersistableInterface;

class Image extends BaseImage implements PersistableInterface
{
    /**
     * @var string
     */
    protected $assetId;

    /**
     * @var ArticleMediaInterface
     */
    protected $media;

    /**
     * @var ArrayCollection
     */
    protected $renditions;

    public function __construct()
    {
        $this->renditions = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @return ArrayCollection
     */
    public function getRenditions()
    {
        return $this->renditions;
    }

    /**
     * @param ImageRenditionInterface $rendition
     */
    public function addRendition(ImageRenditionInterface $rendition)
    {
        $this->renditions->add($rendition);
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
     * @return string
     */
    public function getAssetId(): string
    {
        return $this->assetId;
    }

    /**
     * @param string $assetId
     */
    public function setAssetId(string $assetId)
    {
        $this->assetId = $assetId;
    }
}
