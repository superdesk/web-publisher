<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\TemplatesSystem\Gimme\Widget;

use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractWidgetHandler implements WidgetHandlerInterface, ContainerAwareInterface
{
    const WIDGET_TEMPLATE_PATH = 'widgets';

    protected static $expectedParameters = [];

    protected $container;

    protected $widgetModel;

    /**
     * @return array
     */
    public static function getExpectedParameters()
    {
        return static::$expectedParameters;
    }

    /**
     * AbstractWidgetHandler constructor.
     *
     * @param WidgetModelInterface $widgetModel
     */
    public function __construct(WidgetModelInterface $widgetModel)
    {
        $this->widgetModel = $widgetModel;
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected function getModelParameter($name)
    {
        if (isset($this->widgetModel->getParameters()[$name])) {
            return $this->widgetModel->getParameters()[$name];
        }

        // Get default value
        if (isset(self::getExpectedParameters()[$name])) {
            $parameterMetaData = self::getExpectedParameters()[$name];
            if (is_array($parameterMetaData) && isset($parameterMetaData['default'])) {
                return $parameterMetaData['default'];
            }
        }

        return;
    }

    /**
     * Check if widget should be rendered.
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->widgetModel->getVisible();
    }

    /**
     * Render given template with given parameters.
     */
    protected function renderTemplate($templateName, $parameters = null)
    {
        if (null === $parameters) {
            $parameters = $this->getAllParametersWithValue();
        }

        return $this->container->get('templating')->render(self::WIDGET_TEMPLATE_PATH.'/'.$templateName, $parameters);
    }

    /**
     * Returns associative array with all expected parameters and their values.
     *
     * @return array
     */
    protected function getAllParametersWithValue()
    {
        $all = array();
        foreach (self::getExpectedParameters() as $key => $value) {
            $all[$key] = $this->getModelParameter($key);
        }

        return $all;
    }
}
