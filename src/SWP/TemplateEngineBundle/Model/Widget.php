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

use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\TemplatesSystem\Gimme\Model\WidgetInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Widget.
 */
class Widget implements WidgetInterface
{
    const TYPE_HTML = 1;

    protected $types = [
        self::TYPE_HTML => '\\SWP\\TemplatesSystem\\Gimme\\Widget\\HtmlWidget',
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

    public function __construct()
    {
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
     * @return Widget
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
     * @return self
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
     * @return self
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
     * @return self
     */
    public function setParameters($parameters = array())
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
     * @return self
     */
    protected function setContainers(ArrayCollection $containers)
    {
        $this->containers = $containers;

        return $this;
    }
}
