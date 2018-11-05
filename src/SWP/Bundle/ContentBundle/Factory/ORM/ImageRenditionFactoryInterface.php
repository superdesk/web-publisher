<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Factory\ORM;

use SWP\Bundle\ContentBundle\Model\ArticleMediaInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

interface ImageRenditionFactoryInterface extends FactoryInterface
{
    public function createWith(ArticleMediaInterface $articleMedia, ImageInterface $image, RenditionInterface $rendition): ImageRenditionInterface;
}
