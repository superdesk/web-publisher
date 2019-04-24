<?php

declare(strict_types=1);

namespace SWP\Component\Seo\Model;

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use Symfony\Component\HttpFoundation\File\File;

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

    public function getMetaImageFile(): ?File;

    public function setMetaImageFile(?File $metaImageFile): void;

    public function getMetaImageName(): ?string;

    public function setMetaImageName(?string $metaImageName): void;

    public function getOgImageFile(): ?File;

    public function setOgImageFile(?File $ogImageFile): void;

    public function getOgImageName(): ?string;

    public function setOgImageName(?string $ogImageName): void;

    public function getTwitterImageFile(): ?File;

    public function setTwitterImageFile(?File $twitterImageFile): void;

    public function getTwitterImageName(): ?string;

    public function setTwitterImageName(?string $twitterImageName): void;
}
