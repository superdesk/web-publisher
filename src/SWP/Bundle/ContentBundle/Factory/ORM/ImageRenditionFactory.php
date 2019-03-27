<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Factory\ORM;

use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

final class ImageRenditionFactory implements ImageRenditionFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    public function createWith(ArticleMediaInterface $articleMedia, ImageInterface $image, RenditionInterface $rendition): ImageRenditionInterface
    {
        $imageRendition = $this->create();
        $imageRendition->setImage($image);
        $imageRendition->setMedia($articleMedia);
        $imageRendition->setHeight($rendition->getHeight() ?? $image->getHeight());
        $imageRendition->setWidth($rendition->getWidth() ?? $image->getWidth());
        $imageRendition->setName($rendition->getName());
        $imageRendition->setPreviewUrl($rendition->getHref());

        return $imageRendition;
    }

    public function create(): ImageRenditionInterface
    {
        return $this->decoratedFactory->create();
    }
}
