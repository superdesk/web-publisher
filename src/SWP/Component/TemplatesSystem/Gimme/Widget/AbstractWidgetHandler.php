<?php
/**
 * Created by PhpStorm.
 * User: sourcefabric
 * Date: 15/06/16
 * Time: 16:15
 */

namespace SWP\Component\TemplatesSystem\Gimme\Widget;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;

abstract class AbstractWidgetHandler implements WidgetHandlerInterface, ContainerAwareInterface
{
    protected static $expectedParameters = array();

    protected $container;

    protected $widgetModel;

    public static function getExpectedParameters()
    {
        return static::$expectedParameters;
    }

    public function __construct(WidgetModelInterface $widgetModel)
    {
        $this->widgetModel = $widgetModel;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function getModelParameter($name)
    {
        if (array_key_exists($name, $this->widgetModel->getParameters())) {
            return $this->widgetModel->getParameters()[$name];
        }

        // Get default value
        if (array_key_exists(self::getExpectedParameters()[$name])) {
            $parameterMetaData = self::getExpectedParameters()[$name];
            if (is_array($parameterMetaData) && array_key_exists($parameterMetaData['default'])) {
                return $parameterMetaData['default'];
            }
        }

        // TODO - what if there is no parameter, and default value for that parameter?
        return '';
    }

    /**
     * Check if widget should be rendered
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->widgetModel->getVisible();
    }
}
