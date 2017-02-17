<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TimestampableTrait;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerDataInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;

/**
 * Container.
 */
class Container implements ContainerInterface, TimestampableInterface
{
    use TimestampableTrait;

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
     * Container constructor.
     */
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * {@inheritdoc}
     */
    public function setStyles(string $styles)
    {
        $this->styles = $styles;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    /**
     * {@inheritdoc}
     */
    public function setCssClass(string $cssClass)
    {
        $this->cssClass = $cssClass;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * {@inheritdoc}
     */
    public function setVisible(bool $visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(ArrayCollection $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addData(ContainerDataInterface $containerData)
    {
        $this->data->add($containerData);

        return $this;
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
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

    /**
     * {@inheritdoc}
     */
    public function setWidgets(ArrayCollection $widgets)
    {
        $this->widgets = $widgets;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addWidget($widget)
    {
        $this->widgets->add($widget);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeWidget($widget)
    {
        $this->widgets->removeElement($widget);

        return $this;
    }
}
