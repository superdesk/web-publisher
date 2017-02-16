<?php

/*
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

abstract class AbstractWidgetHandler implements WidgetHandlerInterface
{
    protected static $expectedParameters = [];

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
     * {@inheritdoc}
     */
    public function isVisible()
    {
        return $this->widgetModel->getVisible();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->widgetModel->getId();
    }

    /**
     * @param $name
     *
     * @return null|string
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
