<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Bundle\ContentBundle\Model\ImageRenditionInterface as BaseInterface;

interface ImageRenditionInterface extends BaseInterface
{
    public function getPreviewUrl(): ?string;

    public function setPreviewUrl(?string $previewUrl);
}
