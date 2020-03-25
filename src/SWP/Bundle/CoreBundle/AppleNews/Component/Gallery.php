<?php

namespace SWP\Bundle\CoreBundle\AppleNews\Component;

/**
 * The component for displaying a sequence of images in a specific order as a horizontal strip.
 */
class Gallery implements ComponentInterface
{
    public const ROLE = 'gallery';

    /** @var string */
    private $role = self::ROLE;

    /** @var GalleryItem[] */
    private $items = [];

    public function getRole(): string
    {
        return $this->role;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(GalleryItem $galleryItem): void
    {
        $this->items[] = $galleryItem;
    }
}
