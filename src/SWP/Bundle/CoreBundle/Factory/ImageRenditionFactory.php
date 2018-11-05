<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Factory;

use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Bundle\ContentBundle\Factory\ORM\ImageRenditionFactory as BaseImageRenditionFactory;
use SWP\Component\Storage\Factory\FactoryInterface;

class ImageRenditionFactory extends BaseImageRenditionFactory
{
    public function __construct( $decoratedFactory)
    {
        parent::__construct($decoratedFactory);
    }

    public function createWith(ArticleMediaInterface $articleMedia, ImageInterface $image, RenditionInterface $rendition): ImageRenditionInterface
    {
        $imageRendition = parent::createWith($articleMedia, $image, $rendition);
        $imageRendition->setPreviewUrl($rendition->getHref());

        return $imageRendition;
    }
}
