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

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface SeoMetadataInterface extends PersistableInterface, TimestampableInterface
{
    public function setId(?string $id): void;

    public function getMetaTitle(): ?string;

    public function setMetaTitle(?string $metaTitle): void;

    public function getMetaDescription(): ?string;

    public function setMetaDescription(?string $metaDescription): void;

    public function getOgTitle(): ?string;

    public function setOgTitle(?string $ogTitle): void;

    public function getOgDescription(): ?string;

    public function setOgDescription(?string $ogDescription): void;

    public function getTwitterTitle(): ?string;

    public function setTwitterTitle(?string $twitterTitle): void;

    public function getTwitterDescription(): ?string;

    public function setTwitterDescription(?string $twitterDescription): void;
}
