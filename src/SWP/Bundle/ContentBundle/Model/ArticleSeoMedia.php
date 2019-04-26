<?php

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Seo\Model\SeoImage;

class ArticleSeoMedia extends SeoImage implements ArticleSeoMediaInterface
{
    /**
     * @var ImageInterface
     */
    protected $image;

    public function getImage(): ImageInterface
    {
        return $this->image;
    }

    public function setImage(ImageInterface $image): void
    {
        $this->image = $image;
    }
}
