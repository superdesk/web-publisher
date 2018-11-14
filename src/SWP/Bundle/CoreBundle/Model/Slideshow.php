<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SWP\Bundle\ContentBundle\Model\Slideshow as BaseSlideshow;
use SWP\Bundle\ContentBundle\Model\SlideshowItemInterface;

class Slideshow extends BaseSlideshow implements SlideshowInterface
{
    protected $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();

        parent::__construct();
    }

    public function addSlideshowItem(SlideshowItemInterface $slideshowItem): void
    {
        $slideshowItem->setSlideshow($this);
        $this->items->add($slideshowItem);
    }

    public function getSlideshowItems(): Collection
    {
        return $this->items;
    }
}
