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

use SWP\Component\Seo\Model\SeoMetadataInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ArticleSeoMetadataInterface extends SeoMetadataInterface
{
    public function getMetaMediaFile(): ?UploadedFile;

    public function setMetaMediaFile(?UploadedFile $metaMediaFile): void;

    public function getOgMediaFile(): ?UploadedFile;

    public function setOgMediaFile(?UploadedFile $ogMediaFile): void;

    public function getTwitterMediaFile(): ?UploadedFile;

    public function setTwitterMediaFile(?UploadedFile $twitterMediaFile): void;
}
