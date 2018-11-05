<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\ImageRendition as BaseImageRendition;

class ImageRendition extends BaseImageRendition implements ImageRenditionInterface
{
    /**
     * @var string|null
     */
    protected $previewUrl;

    public function setPreviewUrl(?string $previewUrl): void
    {
        $this->previewUrl = $previewUrl;
    }

    public function getPreviewUrl(): ?string
    {
        return $this->previewUrl;
    }
}
