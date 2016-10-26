<?php

declare(strict_types=1);

/**
 * This file is part of the Superdesk Web Publisher Content List Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\ContentList\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SWP\Component\Common\Model\EnableableTrait;
use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;

class ContentList implements ContentListInterface
{
    use TimestampableTrait, SoftDeletableTrait, EnableableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $cacheLifeTime;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var Collection|ContentListItemInterface[]
     */
    protected $items;

    /**
     * ContentList constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->items = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(string $description = null)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheLifeTime()
    {
        return $this->cacheLifeTime;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheLifeTime(int $cacheLifeTime = 0)
    {
        $this->cacheLifeTime = $cacheLifeTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit(int $limit = 0)
    {
        $this->limit = $limit;
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
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(ContentListItemInterface $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(ContentListItemInterface $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }
    }
}
