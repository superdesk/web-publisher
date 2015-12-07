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

namespace SWP\TemplateEngineBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\TemplatesSystem\Gimme\Model\ContainerInterface;

/**
 * Container.
 */
class Container implements ContainerInterface
{
    const TYPE_SIMPLE = 1;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var integer
     */
    protected $width;

    /**
     * @var integer
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
     * @var boolean
     */
    protected $visible;

    /**
     * @var ArrayCollection
     */
    protected $data;


    public function __construct()
    {
        $this->data = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Page
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
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Sets the value of width.
     *
     * @param integer $width the width
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
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Sets the value of height.
     *
     * @param integer $height the height
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
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Sets the value of visible.
     *
     * @param boolean $visible the visible
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
     * Gets the value of type.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the value of type.
     *
     * @param integer $type the type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
