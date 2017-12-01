<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Menu Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MenuBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MenuItem implements MenuItemInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var MenuItemInterface
     */
    protected $root;

    /**
     * @var int
     */
    protected $lft;

    /**
     * @var int
     */
    protected $rgt;

    /**
     * @var int
     */
    protected $level;

    /**
     * Name of this menu item (used for id by parent menu).
     *
     * @var string
     */
    protected $name = null;

    /**
     * Label to output.
     *
     * @var string
     */
    protected $label = null;

    /**
     * Attributes for the item link.
     *
     * @var array
     */
    protected $linkAttributes = [];

    /**
     * Attributes for the children list.
     *
     * @var array
     */
    protected $childrenAttributes = [];

    /**
     * Attributes for the item text.
     *
     * @var array
     */
    protected $labelAttributes = [];

    /**
     * Uri to use in the anchor tag.
     *
     * @var string
     */
    protected $uri = null;

    /**
     * Attributes for the item.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Extra stuff associated to the item.
     *
     * @var array
     */
    protected $extras = [];

    /**
     * Whether the item is displayed.
     *
     * @var bool
     */
    protected $display = true;

    /**
     * Whether the children of the item are displayed.
     *
     * @var bool
     */
    protected $displayChildren = true;

    /**
     * Child items.
     *
     * @var Collection|ItemInterface[]
     */
    protected $children = [];

    /**
     * Parent item.
     *
     * @var ItemInterface|null
     */
    protected $parent = null;

    /**
     * whether the item is current. null means unknown.
     *
     * @var bool|null
     */
    protected $isCurrent = null;

    /**
     * @var int
     */
    protected $position;

    /**
     * MenuItem constructor.
     */
    public function __construct()
    {
        $this->setChildren([]);
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
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * {@inheritdoc}
     */
    public function getLeft(): int
    {
        return $this->lft;
    }

    /**
     * {@inheritdoc}
     */
    public function setLeft(int $left)
    {
        $this->lft = $left;
    }

    /**
     * {@inheritdoc}
     */
    public function getRight(): int
    {
        return $this->rgt;
    }

    /**
     * @param int $right
     */
    public function setRight(int $right)
    {
        $this->rgt = $right;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function setLevel(int $level)
    {
        $this->level = $level;
    }

    /**
     * {@inheritdoc}
     */
    public function setFactory(FactoryInterface $factory)
    {
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkAttributes()
    {
        return $this->linkAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setLinkAttributes(array $linkAttributes)
    {
        $this->linkAttributes = $linkAttributes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkAttribute($name, $default = null)
    {
        if (isset($this->linkAttributes[$name])) {
            return $this->linkAttributes[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setLinkAttribute($name, $value)
    {
        $this->linkAttributes[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrenAttributes()
    {
        return $this->childrenAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setChildrenAttributes(array $childrenAttributes)
    {
        $this->childrenAttributes = $childrenAttributes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrenAttribute($name, $default = null)
    {
        if (isset($this->childrenAttributes[$name])) {
            return $this->childrenAttributes[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setChildrenAttribute($name, $value)
    {
        $this->childrenAttributes[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelAttributes(array $labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelAttribute($name, $default = null)
    {
        if (isset($this->labelAttributes[$name])) {
            return $this->labelAttributes[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelAttribute($name, $value)
    {
        $this->labelAttributes[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtra($name, $default = null)
    {
        if (isset($this->extras[$name])) {
            return $this->extras[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtra($name, $value)
    {
        $this->extras[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayChildren()
    {
        return $this->displayChildren;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayChildren($bool)
    {
        $this->displayChildren = (bool) $bool;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayed()
    {
        return $this->display;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplay($bool)
    {
        $this->display = (bool) $bool;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChild(ItemInterface $menuItem): bool
    {
        return $this->children->contains($menuItem);
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($child, array $options = array())
    {
        if (!$this->hasChild($child)) {
            $child->setParent($this);
            $this->children->set($child->getName(), $child);
        }

        return $child;
    }

    /**
     * {@inheritdoc}
     */
    public function getChild($name)
    {
        return $this->children->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function reorderChildren($order)
    {
        if (count($order) != $this->count()) {
            throw new \InvalidArgumentException('Cannot reorder children, order does not contain all children.');
        }

        $newChildren = array();
        foreach ($order as $name) {
            if (!$this->children->containsKey($name)) {
                throw new \InvalidArgumentException('Cannot find children named '.$name);
            }
            $child
                = $this->getChild($name);
            $newChildren[$name] = $child;
        }

        $this->setChildren($newChildren);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function copy()
    {
        $newMenu = clone $this;
        $newMenu->children = new ArrayCollection();
        $newMenu->setParent(null);
        foreach ($this->getChildren() as $child) {
            $newMenu->addChild($child->copy());
        }

        return $newMenu;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot()
    {
        return null === $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(ItemInterface $parent = null)
    {
        if ($parent === $this) {
            throw new \InvalidArgumentException('Item cannot be a child of itself');
        }

        $this->parent = $parent;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        if (is_array($this->children)) {
            return $this->children;
        }

        return $this->children->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function setChildren(array $children)
    {
        $this->children = new ArrayCollection($children);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild($name)
    {
        $name = $name instanceof ItemInterface ? $name->getName() : $name;
        $child = $this->getChild($name);

        if (null !== $child) {
            $child->setParent(null);
            $this->children->remove($name);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstChild()
    {
        return $this->children->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastChild()
    {
        return $this->children->last();
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        foreach ($this->children as $child) {
            if ($child->isDisplayed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrent($bool)
    {
        $this->isCurrent = $bool;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isCurrent()
    {
        return $this->isCurrent;
    }

    /**
     * {@inheritdoc}
     */
    public function isLast()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return $this->getParent()->getLastChild() === $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isFirst()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return $this->getParent()->getFirstChild() === $this;
    }

    /**
     * {@inheritdoc}
     */
    public function actsLikeFirst()
    {
        // root items are never "marked" as first
        if ($this->isRoot()) {
            return false;
        }

        // A menu acts like first only if it is displayed
        if (!$this->isDisplayed()) {
            return false;
        }

        // if we're first and visible, we're first, period.
        if ($this->isFirst()) {
            return true;
        }

        $children = $this->getParent()->getChildren();
        foreach ($children as $child) {
            // loop until we find a visible menu. If its this menu, we're first
            if ($child->isDisplayed()) {
                return $child->getName() === $this->getName();
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function actsLikeLast()
    {
        // root items are never "marked" as last
        if ($this->isRoot()) {
            return false;
        }

        // A menu acts like last only if it is displayed
        if (!$this->isDisplayed()) {
            return false;
        }

        // if we're last and visible, we're last, period.
        if ($this->isLast()) {
            return true;
        }

        /** @var ItemInterface[] $children */
        $children = array_reverse($this->getParent()->getChildren());
        foreach ($children as $child) {
            // loop until we find a visible menu. If its this menu, we're first
            if ($child->isDisplayed()) {
                return $child->getName() === $this->getName();
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($name)
    {
        return $this->getChild($name);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($name, $value)
    {
        return $this->addChild($name)->setLabel($value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($name)
    {
        $this->removeChild($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(int $position)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId()
    {
        /** @var MenuItemInterface $parent */
        if (null !== $parent = $this->getParent()) {
            return (int) $parent->getId();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRootId()
    {
        if (null !== $this->root) {
            return (int) $this->root->getId();
        }
    }
}
