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

use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\Image as BaseImage;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Component\Storage\Model\PersistableInterface;

class Image extends BaseImage implements PersistableInterface
{
    /**
     * @var ArticleMediaInterface
     */
    protected $media;

    /**
     * @var ImageRenditionInterface
     */
    protected $rendition;

    /**
     * @return ImageRenditionInterface
     */
    public function getRendition()
    {
        return $this->rendition;
    }

    /**
     * @param ImageRenditionInterface $rendition
     */
    public function setRendition(ImageRenditionInterface $rendition)
    {
        $this->rendition = $rendition;
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
}
