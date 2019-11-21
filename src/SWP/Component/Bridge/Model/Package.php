<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SWP\Component\Bridge\Model\Item as BridgeItem;
use SWP\Component\Common\Model\EnableableTrait;
use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;

class Package extends BaseContent implements PackageInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;
    use EnableableTrait;

    /**
     * @var Collection
     */
    protected $items;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var Collection
     */
    protected $externalData;

    /**
     * @var Collection
     */
    protected $groups;

    /**
     * @var ItemInterface[]|Collection
     */
    protected $relatedItems;

    protected $featureMedia;

    public function __construct()
    {
        parent::__construct();

        $this->items = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->externalData = new ArrayCollection();
        $this->relatedItems = new ArrayCollection();
    }

    public function getItemsArray(): array
    {
        return $this->items->toArray();
    }

    public function getGroupsArray(): array
    {
        return $this->groups->toArray();
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items)
    {
        $this->items = $items;
    }

    public function addItem(BridgeItem $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setPackage($this);
        }

        return $this;
    }

    public function removeItem(BridgeItem $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $item->setPackage(null);
        }
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getExternalData(): ?Collection
    {
        return $this->externalData;
    }

    public function setExternalData(Collection $externalData): void
    {
        $this->externalData = $externalData;
    }

    public function getGroups(): ?Collection
    {
        return $this->groups;
    }

    public function setGroups(?Collection $groups): void
    {
        $this->groups = $groups;
    }

    public function getRelatedItems(): Collection
    {
        return $this->relatedItems;
    }

    public function setRelatedItems(Collection $relatedItems): void
    {
        $this->relatedItems = $relatedItems;
    }

    public function getFeatureMedia(): ?ItemInterface
    {
        return $this->featureMedia;
    }

    public function setFeatureMedia(ItemInterface $featureMedia): void
    {
        $this->featureMedia = $featureMedia;
    }
}
