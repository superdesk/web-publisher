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
namespace SWP\Bundle\TemplateEngineBundle\Container;

use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;

class SimpleContainer
{
    /**
     * @var ContainerInterface
     */
    protected $containerEntity;

    /**
     * @var \Twig_Environment
     */
    protected $renderer;

    /**
     * @var array
     */
    protected $widgets = [];

    /**
     * @param ContainerInterface $containerEntity
     * @param \Twig_Environment  $renderer
     */
    public function __construct(ContainerInterface $containerEntity, \Twig_Environment $renderer)
    {
        $this->containerEntity = $containerEntity;
        $this->renderer = $renderer;
    }

    /**
     * Set Widgets.
     *
     * @param array $widgets
     */
    public function setWidgets($widgets)
    {
        $this->widgets = $widgets;

        return $this;
    }

    /**
     * Render open tag for container.
     *
     * @return string
     */
    public function renderOpenTag()
    {
        return $this->renderer->render('open_tag', [
            'id' => $this->containerEntity->getId(),
            'class' => $this->containerEntity->getCssClass(),
            'height' => $this->containerEntity->getHeight(),
            'width' => $this->containerEntity->getWidth(),
            'styles' => $this->containerEntity->getStyles(),
            'visible' => $this->containerEntity->getVisible(),
            'data' => $this->containerEntity->getData(),
        ]);
    }

    /**
     * Check if container has items.
     *
     * @return bool
     */
    public function hasWidgets()
    {
        if (count($this->widgets) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Go through widgets render them and collect output of rendering.
     *
     * @return string
     */
    public function renderWidgets()
    {
        $widgetsOutput = [];
        foreach ($this->widgets as $widget) {
            $widgetsOutput[] = $widget->render();
        }

        return implode("\n", $widgetsOutput);
    }

    /**
     * Check if container is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->containerEntity->getVisible();
    }

    /**
     * Render close tag for container.
     *
     * @return string
     */
    public function renderCloseTag()
    {
        return $this->renderer->render('close_tag');
    }
}
