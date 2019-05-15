<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Seo Component.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Seo\Model;

trait SeoMetadataAwareTrait
{
    /**
     * @var SeoMetadataInterface|null
     */
    protected $seoMetadata;

    public function getSeoMetadata(): ?SeoMetadataInterface
    {
        return $this->seoMetadata;
    }

    public function setSeoMetadata(?SeoMetadataInterface $seoMetadata): void
    {
        $this->seoMetadata = $seoMetadata;
    }
}
