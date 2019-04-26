<?php

declare(strict_types=1);

namespace SWP\Component\Seo\Model;

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface SeoMetadataInterface extends PersistableInterface, TimestampableInterface
{
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
