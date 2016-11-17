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

use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\ContainerWidgetInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;

/**
 * ContainerWidget.
 */
class ContainerWidget implements ContainerWidgetInterface
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

    /**
     * ContainerWidget constructor.
     *
     * @param ContainerInterface   $container
     * @param WidgetModelInterface $widget
     */
    public function __construct(ContainerInterface $container, WidgetModelInterface $widget)
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
     * {@inheritdoc}
     */
    public function getWidget()
    {
        return $this->widget;
    }
}
