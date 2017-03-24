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
use SWP\Bundle\TemplatesSystemBundle\Widget\GoogleAdSenseWidgetHandler;
use SWP\Bundle\TemplatesSystemBundle\Widget\MenuWidgetHandler;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
use SWP\Component\TemplatesSystem\Gimme\Widget\HtmlWidgetHandler;

/**
 * WidgetModel.
 */
class WidgetModel implements WidgetModelInterface, TimestampableInterface, PersistableInterface
{
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
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt = null;

    /**
     * WidgetModel constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->parameters = [];
        $this->setVisible();
        $this->setType();
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes(): array
    {
        return [
            self::TYPE_HTML => HtmlWidgetHandler::class,
            self::TYPE_ADSENSE => GoogleAdSenseWidgetHandler::class,
            self::TYPE_MENU => MenuWidgetHandler::class,
        ];
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
     * @param string $id
     *
     * @return WidgetModel
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
        if (array_key_exists($type, $this->getTypes())) {
            $this->type = $this->getTypes()[$type];
        } else {
            if (in_array($type, $this->getTypes())) {
                $this->type = $type;
            } else {
                $this->type = $this->getTypes()[self::TYPE_HTML];
            }
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
     * @param array $parameters the parameters
     *
     * @return WidgetModel
     */
    public function setParameters(array $parameters = [])
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
}
