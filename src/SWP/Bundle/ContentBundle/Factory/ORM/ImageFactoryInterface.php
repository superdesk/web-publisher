<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Factory\ORM;

use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Component\Bridge\Model\RenditionInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

interface ImageFactoryInterface extends FactoryInterface
{
    public function createFromRendition(RenditionInterface $rendition): ?ImageInterface;
}
