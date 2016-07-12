<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\TemplatesSystem\Tests\Gimme\Model;

use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;

/**
 * WidgetModel.
 */
class WidgetModel implements WidgetModelInterface
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
     * @var array
     */
    protected $parameters;

    public function __construct()
    {
        $this->parameters = [];
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
    public function setVisible($visible)
    {
        $this->visible = $visible;

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
     * Gets the value of parameters.
     *
     * @return array
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
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }
}
