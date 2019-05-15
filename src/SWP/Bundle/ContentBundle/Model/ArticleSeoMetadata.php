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

use SWP\Component\Seo\Model\SeoMetadata;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleSeoMetadata extends SeoMetadata implements ArticleSeoMetadataInterface
{
    /**
     * @var UploadedFile|null
     */
    protected $metaMediaFile;

    /**
     * @var UploadedFile|null
     */
    protected $ogMediaFile;

    /**
     * @var UploadedFile|null
     */
    protected $twitterMediaFile;

    /**
     * @var string|null
     */
    protected $packageGuid;

    public function getMetaMediaFile(): ?UploadedFile
    {
        return $this->metaMediaFile;
    }

    public function setMetaMediaFile(?UploadedFile $metaMediaFile): void
    {
        $this->metaMediaFile = $metaMediaFile;
    }

    public function getOgMediaFile(): ?UploadedFile
    {
        return $this->ogMediaFile;
    }

    public function setOgMediaFile(?UploadedFile $ogMediaFile): void
    {
        $this->ogMediaFile = $ogMediaFile;
    }

    public function getTwitterMediaFile(): ?UploadedFile
    {
        return $this->twitterMediaFile;
    }

    public function setTwitterMediaFile(?UploadedFile $twitterMediaFile): void
    {
        $this->twitterMediaFile = $twitterMediaFile;
    }

    public function getPackageGuid(): ?string
    {
        return $this->packageGuid;
    }

    public function setPackageGuid(?string $packageGuid): void
    {
        $this->packageGuid = $packageGuid;
    }
}
