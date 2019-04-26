<?php

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Seo\Model\SeoImageInterface;

interface ArticleSeoMediaInterface extends SeoImageInterface
{
    public const MEDIA_META = '';

    public const MEDIA_OG = '';

    public const MEDIA_TWITTER_KEY = '';

    public function getImage(): ImageInterface;

    public function setImage(ImageInterface $image): void;
}
