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
use SWP\TemplateEngineBundle\Model\Widget;

/**
 * ContainerWidget.
 */
class ContainerWidget
{
    /**
     * @var integer
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
     * @var integer
     */
    protected $position;

    public function __construct(Container $container, Widget $widget)
    {
        $this->container = $container;
        $this->widget = $widget;
        $this->position = -1;
    }

    /**
     * Set widget position
     *
     * @param integer $position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get Widget
     *
     * @return Widget
     */
    public function getWidget()
    {
        return $this->widget;
    }
}
