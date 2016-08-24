<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Interface ArticleMediaInterface.
 */
interface MediaAwareArticleInterface
{
    /**
     * @param Collection $media
     *
     * @return mixed
     */
    public function setMedia(Collection $media);

    /**
     * @return Collection
     */
    public function getMedia();
}