<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

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
