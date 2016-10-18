<?php

namespace SWP\Bundle\MenuBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem as BaseMenuItem;

class MenuItem extends BaseMenuItem implements MenuItemInterface
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
     * @var MenuItemInterface
     */
    protected $parent;

    /**
     * @var Collection|MenuItemInterface[]
     */
    protected $children = [];

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
     * MenuItem constructor.
     *
     * @param string           $name
     * @param FactoryInterface $factory
     */
    public function __construct($name, FactoryInterface $factory)
    {
        parent::__construct($name, $factory);

        $this->children = new ArrayCollection();
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
    public function addChild($child, array $options = array())
    {
        if (!($child instanceof ItemInterface)) {
            $child = $this->factory->createItem($child, $options);
        } elseif (null !== $child->getParent()) {
            throw new \InvalidArgumentException('Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).');
        }

        $child->setParent($this);
        $this->children->set($child->getName(), $child);

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
}
