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

namespace SWP\Bundle\ContentBundle\Model;

use DateTime;
use Doctrine\Common\Collections\Collection;

interface MediaAwareInterface
{
    public const KEY_FEATURE_MEDIA = 'featuremedia';

    public function getMedia(): Collection;

    public function setMedia(Collection $media): void;

    public function getFeatureMedia(): ?ArticleMediaInterface;

    public function setFeatureMedia(ArticleMediaInterface $featureMedia = null): void;

    public function getMediaUpdatedAt(): ?DateTime;

    public function setMediaUpdatedAt(DateTime $mediaUpdatedAt): void;
}
