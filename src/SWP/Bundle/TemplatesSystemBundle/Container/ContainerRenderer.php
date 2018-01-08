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

namespace SWP\Bundle\TemplatesSystemBundle\Container;

use SWP\Component\TemplatesSystem\Gimme\Model\ContainerInterface;
use SWP\Component\TemplatesSystem\Gimme\Widget\WidgetHandlerInterface;

/**
 * Class ContainerRenderer.
 */
class ContainerRenderer implements ContainerRendererInterface
{
    const WIDGET_CLASS = 'swp_widget';

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
     * @var string
     */
    protected $cacheDir;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * ContainerRenderer constructor.
     *
     * @param ContainerInterface     $containerEntity
     * @param \Twig_Environment|null $renderer
     * @param bool                   $debug
     * @param null                   $cacheDir
     */
    public function __construct(ContainerInterface $containerEntity, \Twig_Environment $renderer = null, $debug = false, $cacheDir = null)
    {
        $this->containerEntity = $containerEntity;
        $this->debug = $debug;
        $this->cacheDir = $cacheDir;

        if (null === $renderer) {
            $renderer = $this->getRenderer();
        }
        $this->renderer = $renderer;
    }

    /**
     * Set Widgets.
     *
     * @param array $widgets
     *
     * @return self
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
        /** @var WidgetHandlerInterface $widget */
        foreach ($this->widgets as $widget) {
            $widgetsOutput[] = sprintf(
                '<div id="%s_%s" class="%s">%s</div>',
                self::WIDGET_CLASS,
                $widget->getId(),
                self::WIDGET_CLASS,
                $widget->render()
            );
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

    /**
     * @return \Twig_Environment
     */
    private function getRenderer()
    {
        $options = [];
        if (false === $this->debug && null !== $this->cacheDir) {
            $options['cache'] = $this->cacheDir.'/twig';
        }

        $this->renderer = new \Twig_Environment(
            new \Twig_Loader_Array([
                'open_tag' => ContainerRendererInterface::OPEN_TAG_TEMPLATE,
                'close_tag' => ContainerRendererInterface::CLOSE_TAG_TEMPLATE,
            ]),
            $options
        );

        return $this->renderer;
    }
}
