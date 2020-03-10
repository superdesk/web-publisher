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
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TimestampableTrait;

class Item extends BaseContent implements ItemInterface, TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $bodyText;

    /**
     * Collection.
     */
    protected $renditions;

    /**
     * @var string
     */
    protected $usageTerms;

    /**
     * @var ArrayCollection
     */
    public $items;

    /**
     * @var Package
     */
    protected $package;

    /**
     * @var Package
     */
    protected $group;

    /** @var int|null */
    protected $position;

    public function __construct()
    {
        parent::__construct();
        $this->renditions = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function setRenditions(Collection $renditions)
    {
        $this->renditions = $renditions;
    }

    /**
     * {@inheritdoc}
     */
    public function getRenditions(): Collection
    {
        return $this->renditions;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ArrayCollection $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getBodyText()
    {
        return $this->bodyText;
    }

    /**
     * @param string $bodyText
     */
    public function setBodyText($bodyText)
    {
        $this->bodyText = $bodyText;
    }

    /**
     * @return string
     */
    public function getUsageTerms()
    {
        return $this->usageTerms;
    }

    /**
     * @param string $usageTerms
     */
    public function setUsageTerms($usageTerms)
    {
        $this->usageTerms = $usageTerms;
    }

    /**
     * Set package.
     *
     * @param PackageInterface|void $package
     *
     * @return Item
     */
    public function setPackage(PackageInterface $package = null)
    {
        $this->package = $package;
    }

    /**
     * Get package.
     *
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    public function setGroup(?GroupInterface $group): void
    {
        $this->group = $group;
    }

    public function getGroup(): ?GroupInterface
    {
        return $this->group;
    }

    public function getItemsArray(): array
    {
        if (null !== $this->items) {
            return $this->items->toArray();
        }

        return [];
    }

    public function getGroupsArray(): array
    {
        return $this->groups->toArray();
    }

    public function getRenditionsArray(): array
    {
        return $this->renditions->toArray();
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }
}
