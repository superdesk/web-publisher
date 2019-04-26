<?php

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Seo\Model\SeoImageInterface;

interface ArticleSeoMediaInterface extends SeoImageInterface
{
    public const MEDIA_META_KEY = 'seo_media_meta';

    public const MEDIA_OG_KEY = 'seo_media_og';

    public const MEDIA_TWITTER_KEY = 'seo_media_twitter';

    public function getImage(): ImageInterface;

    public function setImage(ImageInterface $image): void;
}
