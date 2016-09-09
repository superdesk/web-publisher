<?php

/**
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
use SWP\Component\Common\Model\EnableableTrait;
use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;
use SWP\Component\Storage\Model\PersistableInterface;

class Package extends BaseContent implements PackageInterface, PersistableInterface
{
    use TimestampableTrait, SoftDeletableTrait, EnableableTrait;

    /**
     * @var Collection
     */
    protected $items;

    /**
     * @var string
     */
    protected $body;

    /**
     * Package constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(Collection $items)
    {
        $this->items = $items;
    }

    /**
     * Add item.
     *
     * @param \SWP\Component\Bridge\Model\Item $item
     *
     * @return Package
     */
    public function addItem(\SWP\Component\Bridge\Model\Item $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setPackage($this);
        }

        return $this;
    }

    /**
     * Remove item.
     *
     * @param \SWP\Component\Bridge\Model\Item $item
     */
    public function removeItem(\SWP\Component\Bridge\Model\Item $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $item->setPackage(null);
        }
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
}
