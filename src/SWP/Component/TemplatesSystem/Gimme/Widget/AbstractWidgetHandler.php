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

use SWP\Bundle\TemplatesSystemBundle\Container\ContainerRendererInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;

abstract class AbstractWidgetHandler implements WidgetHandlerInterface
{
    protected static $expectedParameters = [];

    protected $widgetModel;

    public static function getExpectedParameters(): array
    {
        return static::$expectedParameters;
    }

    public function __construct(WidgetModelInterface $widgetModel)
    {
        $this->widgetModel = $widgetModel;
    }

    public function isVisible(): bool
    {
        return $this->widgetModel->getVisible();
    }

    public function getId(): int
    {
        return $this->widgetModel->getId();
    }

    public function renderWidgetOpenTag(string $containerId): string
    {
        return sprintf(
            '<div id="%s_%s" class="%s" data-container="%s">',
            ContainerRendererInterface::WIDGET_CLASS,
            $this->widgetModel->getId(),
            ContainerRendererInterface::WIDGET_CLASS,
            $containerId
        );
    }

    protected function getModelParameter(string $name): ?string
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

        return null;
    }

    protected function getAllParametersWithValue(): array
    {
        $all = array();
        foreach (self::getExpectedParameters() as $key => $value) {
            $all[$key] = $this->getModelParameter($key);
        }

        return $all;
    }
}
