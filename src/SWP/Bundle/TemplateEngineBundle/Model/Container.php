<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;

/**
 * Container.
 */
class Container implements ContainerInterface, TenantAwareInterface, TimestampableInterface
{
    const TYPE_SIMPLE = 1;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var string
     */
    protected $styles;

    /**
     * @var string
     */
    protected $cssClass;

    /**
     * @var bool
     */
    protected $visible;

    /**
     * @var ArrayCollection
     */
    protected $data;

    /**
     * @var ArrayCollection
     */
    protected $widgets;

    /**
     * @var TenantInterface
     */
    protected $tenant;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->data = new ArrayCollection();
        $this->widgets = new ArrayCollection();
        $this->setType(self::TYPE_SIMPLE);
        $this->setVisible(true);
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the value of width.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Sets the value of width.
     *
     * @param int $width the width
     *
     * @return self
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Gets the value of height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Sets the value of height.
     *
     * @param int $height the height
     *
     * @return self
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Gets the value of styles.
     *
     * @return string
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * Sets the value of styles.
     *
     * @param string $styles the styles
     *
     * @return self
     */
    public function setStyles($styles)
    {
        $this->styles = $styles;

        return $this;
    }

    /**
     * Gets the value of cssClass.
     *
     * @return string
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    /**
     * Sets the value of cssClass.
     *
     * @param string $cssClass the css class
     *
     * @return self
     */
    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;

        return $this;
    }

    /**
     * Gets the value of visible.
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Sets the value of visible.
     *
     * @param bool $visible the visible
     *
     * @return self
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Gets the value of data.
     *
     * @return ArrayCollection
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets the value of data.
     *
     * @param ArrayCollection $data the data
     *
     * @return self
     */
    public function setData(ArrayCollection $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Add ContainerData to container.
     *
     * @param ContainerData $containerData
     */
    public function addData(ContainerData $containerData)
    {
        $this->data->add($containerData);

        return $this;
    }

    /**
     * Gets the value of type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the value of type.
     *
     * @param int $type the type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets the value of widgets.
     *
     * @return ArrayCollection
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

    /**
     * Sets the value of widgets.
     *
     * @param ArrayCollection $widgets the widgets
     *
     * @return self
     */
    public function setWidgets(ArrayCollection $widgets)
    {
        $this->widgets = $widgets;

        return $this;
    }

    /**
     * Add widget to container.
     *
     * @param $widget
     */
    public function addWidget($widget)
    {
        $this->widgets->add($widget);

        return $this;
    }

    /**
     * Remove widget to container.
     *
     * @param $widget
     */
    public function removeWidget($widget)
    {
        $this->widgets->removeElement($widget);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function setTenant(TenantInterface $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        if (is_null($this->updatedAt)) {
            return $this->createdAt;
        }

        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
