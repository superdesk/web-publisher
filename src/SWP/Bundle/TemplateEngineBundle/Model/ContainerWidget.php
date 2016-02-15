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
     * @var Widget
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

    public function __construct(Container $container, Widget $widget)
    {
        $this->container = $container;
        $this->widget = $widget;
        $this->position = -1;
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
     * Get Widget.
     *
     * @return Widget
     */
    public function getWidget()
    {
        return $this->widget;
    }
}
