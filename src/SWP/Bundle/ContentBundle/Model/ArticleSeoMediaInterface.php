<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

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
