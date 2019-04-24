<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Seo\Model\SeoMetadataInterface;

interface ArticleSeoMetadataInterface extends SeoMetadataInterface
{
    public function getMetaImage(): ?ImageInterface;

    public function setMetaImage(?ImageInterface $metaImage): void;

    public function getOgImage(): ?ImageInterface;

    public function setOgImage(?ImageInterface $ogImage): void;

    public function getTwitterImage(): ?ImageInterface;

    public function setTwitterImage(?ImageInterface $twitterImage): void;
}
