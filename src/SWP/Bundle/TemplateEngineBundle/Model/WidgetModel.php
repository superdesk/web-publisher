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
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;

/**
 * WidgetModel.
 */
class WidgetModel implements WidgetModelInterface, TenantAwareInterface, TimestampableInterface
{
    const TYPE_HTML = 1;

    protected $types = [
        self::TYPE_HTML => '\\SWP\\Component\\TemplatesSystem\\Gimme\\WidgetModel\\HtmlWidgetHandler',
    ];

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $visible;

    /**
     * @var []
     */
    protected $parameters;

    /**
     * @var ArrayCollection
     */
    protected $containers;

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
        $this->parameters = [];
        $this->setVisible();
        $this->setType();
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
     * @return WidgetModel
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
     * @return WidgetModel
     */
    public function setVisible($visible = true)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Gets the value of type.
     *
     * @return string
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
     * @return WidgetModel
     */
    public function setType($type = self::TYPE_HTML)
    {
        if (array_key_exists($type, $this->types)) {
            $this->type = $this->types[$type];
        } else {
            $this->type = $this->types[self::TYPE_HTML];
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets the value of parameters.
     *
     * @param [] $parameters the parameters
     *
     * @return WidgetModel
     */
    public function setParameters($parameters = [])
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Gets the value of containers.
     *
     * @return ArrayCollection
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * Sets the value of containers.
     *
     * @param ArrayCollection $containers the containers
     *
     * @return WidgetModel
     */
    protected function setContainers(ArrayCollection $containers)
    {
        $this->containers = $containers;

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
