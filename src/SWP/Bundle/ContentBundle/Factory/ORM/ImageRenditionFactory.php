<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Factory\ORM;

use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class ImageRenditionFactory implements ImageRenditionFactoryInterface
{
    protected $decoratedFactory;

    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    public function createWith(ArticleMediaInterface $articleMedia, ImageInterface $image, RenditionInterface $rendition): ImageRenditionInterface
    {
        $imageRendition = $this->create();
        $imageRendition->setImage($image);
        $imageRendition->setMedia($articleMedia);
        $imageRendition->setHeight($rendition->getHeight());
        $imageRendition->setWidth($rendition->getWidth());
        $imageRendition->setName($rendition->getName());

        return $imageRendition;
    }

    public function create(): ImageRenditionInterface
    {
        return $this->decoratedFactory->create();
    }
}
