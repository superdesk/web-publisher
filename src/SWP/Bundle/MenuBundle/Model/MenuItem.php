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
     * Label to output, name is used by default.
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
     * @var ItemInterface[]
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

    public function setFactory(FactoryInterface $factory)
    {
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function getLabel()
    {
        return null !== $this->label ? $this->label : $this->name;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttribute($name, $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return $default;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function getLinkAttributes()
    {
        return $this->linkAttributes;
    }

    public function setLinkAttributes(array $linkAttributes)
    {
        $this->linkAttributes = $linkAttributes;

        return $this;
    }

    public function getLinkAttribute($name, $default = null)
    {
        if (isset($this->linkAttributes[$name])) {
            return $this->linkAttributes[$name];
        }

        return $default;
    }

    public function setLinkAttribute($name, $value)
    {
        $this->linkAttributes[$name] = $value;

        return $this;
    }

    public function getChildrenAttributes()
    {
        return $this->childrenAttributes;
    }

    public function setChildrenAttributes(array $childrenAttributes)
    {
        $this->childrenAttributes = $childrenAttributes;

        return $this;
    }

    public function getChildrenAttribute($name, $default = null)
    {
        if (isset($this->childrenAttributes[$name])) {
            return $this->childrenAttributes[$name];
        }

        return $default;
    }

    public function setChildrenAttribute($name, $value)
    {
        $this->childrenAttributes[$name] = $value;

        return $this;
    }

    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    public function setLabelAttributes(array $labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;

        return $this;
    }

    public function getLabelAttribute($name, $default = null)
    {
        if (isset($this->labelAttributes[$name])) {
            return $this->labelAttributes[$name];
        }

        return $default;
    }

    public function setLabelAttribute($name, $value)
    {
        $this->labelAttributes[$name] = $value;

        return $this;
    }

    public function getExtras()
    {
        return $this->extras;
    }

    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }

    public function getExtra($name, $default = null)
    {
        if (isset($this->extras[$name])) {
            return $this->extras[$name];
        }

        return $default;
    }

    public function setExtra($name, $value)
    {
        $this->extras[$name] = $value;

        return $this;
    }

    public function getDisplayChildren()
    {
        return $this->displayChildren;
    }

    public function setDisplayChildren($bool)
    {
        $this->displayChildren = (bool) $bool;

        return $this;
    }

    public function isDisplayed()
    {
        return $this->display;
    }

    public function setDisplay($bool)
    {
        $this->display = (bool) $bool;

        return $this;
    }

    /**
     * @param ItemInterface $menuItem
     *
     * @return bool
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

    public function isRoot()
    {
        return null === $this->parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(ItemInterface $parent = null)
    {
        if ($parent === $this) {
            throw new \InvalidArgumentException('Item cannot be a child of itself');
        }

        $this->parent = $parent;

        return $this;
    }

    public function getChildren()
    {
        if (is_array($this->children)) {
            return $this->children;
        }

        return $this->children->toArray();
    }

    public function setChildren(array $children)
    {
        $this->children = new ArrayCollection($children);

        return $this;
    }

    public function removeChild($name)
    {
        $name = $name instanceof ItemInterface ? $name->getName() : $name;
        $child = $this->getChild($name);

        if ($child !== null) {
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

    public function hasChildren()
    {
        foreach ($this->children as $child) {
            if ($child->isDisplayed()) {
                return true;
            }
        }

        return false;
    }

    public function setCurrent($bool)
    {
        $this->isCurrent = $bool;

        return $this;
    }

    public function isCurrent()
    {
        return $this->isCurrent;
    }

    public function isLast()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return $this->getParent()->getLastChild() === $this;
    }

    public function isFirst()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return $this->getParent()->getFirstChild() === $this;
    }

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
     * Implements Countable.
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * Implements IteratorAggregate.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }

    /**
     * Implements ArrayAccess.
     */
    public function offsetExists($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * Implements ArrayAccess.
     */
    public function offsetGet($name)
    {
        return $this->getChild($name);
    }

    /**
     * Implements ArrayAccess.
     */
    public function offsetSet($name, $value)
    {
        return $this->addChild($name)->setLabel($value);
    }

    /**
     * Implements ArrayAccess.
     */
    public function offsetUnset($name)
    {
        $this->removeChild($name);
    }
}
