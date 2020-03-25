<?php

namespace SWP\Bundle\CoreBundle\AppleNews\Component;

/**
 * An object used in a gallery or mosaic component for displaying an individual image.
 */
class GalleryItem
{
    /** @var string */
    private $url;

    /** @var string */
    private $caption;

    public function __construct(string $url, string $caption)
    {
        $this->url = $url;
        $this->caption = $caption;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }
}
