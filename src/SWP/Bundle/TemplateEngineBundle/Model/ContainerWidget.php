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

/**
 * ContainerWidget.
 */
class ContainerWidget
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var WidgetModel
     */
    protected $widget;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var int
     */
    protected $position;

    public function __construct(Container $container, WidgetModel $widget)
    {
        $this->container = $container;
        $this->widget = $widget;
        $this->position = -1;
    }

    public function __clone()
    {
        if ($this->getId()) {
            $this->id = null;
        }
    }

    /**
     * Set widget position.
     *
     * @param int $position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get WidgetModel.
     *
     * @return WidgetModel
     */
    public function getWidget()
    {
        return $this->widget;
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
     * Get position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set widget.
     *
     * @param \SWP\Bundle\TemplateEngineBundle\Model\WidgetModel $widget
     *
     * @return ContainerWidget
     */
    public function setWidget(\SWP\Bundle\TemplateEngineBundle\Model\WidgetModel $widget = null)
    {
        $this->widget = $widget;

        return $this;
    }

    /**
     * Set container.
     *
     * @param \SWP\Bundle\TemplateEngineBundle\Model\Container $container
     *
     * @return ContainerWidget
     */
    public function setContainer(\SWP\Bundle\TemplateEngineBundle\Model\Container $container = null)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get container.
     *
     * @return \SWP\Bundle\TemplateEngineBundle\Model\Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
