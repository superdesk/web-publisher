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
namespace SWP\TemplateEngineBundle\Container;

use SWP\TemplatesSystem\Gimme\Model\ContainerInterface;

class SimpleContainer
{
    protected $containerEntity;

    protected $renderer;

    protected $widgets = [];

    public function __construct(ContainerInterface $containerEntity, $renderer)
    {
        $this->containerEntity = $containerEntity;
        $this->renderer = $renderer;
    }

    public function setWidgets($widgets)
    {
        $this->widgets = $widgets;

        return $this;
    }

    public function renderOpenTag()
    {
        return $this->renderer->render('open_tag', array(
            'id' => $this->containerEntity->getId(),
            'class' => $this->containerEntity->getCssClass(),
            'height' => $this->containerEntity->getHeight(),
            'width' => $this->containerEntity->getWidth(),
            'styles' => $this->containerEntity->getStyles(),
            'visible' => $this->containerEntity->getVisible(),
            'data' => $this->containerEntity->getData(),
        ));
    }

    public function hasWidgets()
    {
        if (count($this->widgets) > 0) {
            return true;
        }

        return false;
    }

    public function renderWidgets()
    {
        $widgetsOutput = [];
        foreach ($this->widgets as $widget) {
            $widgetsOutput[] = $widget->render();
        }

        return implode("\n", $widgetsOutput);
    }

    public function isVisible()
    {
        return $this->containerEntity->getVisible();
    }

    public function renderCloseTag()
    {
        return $this->renderer->render('close_tag');
    }
}
