<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\TemplatesSystem\Gimme\Meta;

use SWP\Component\TemplatesSystem\Gimme\Context\Context;

/**
 * Class Meta.
 */
class Meta
{
    /**
     * Original Meta values (json|array|object).
     *
     * @var mixed
     */
    protected $values;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * Create Meta class from provided configuration and values.
     *
     * @param Context             $context
     * @param string|array|object $values
     * @param array               $configuration
     */
    public function __construct(Context $context, $values, $configuration)
    {
        $this->context = $context;
        $this->values = $values;

        $this->configuration = $configuration;

        if (is_array($this->values)) {
            $this->fillFromArray($this->values, $this->configuration);
        } elseif (is_string($this->values) && $this->isJson($this->values)) {
            $this->fillFromArray(json_decode($values, true), $this->configuration);
        } elseif (is_object($this->values)) {
            $this->fillFromObject($this->values, $this->configuration);
        }

        $this->context->registerMeta($this);
    }

    /**
     * Use to_string property from configuration if provided, json with exposed properties otherwise.
     *
     * @return string
     */
    public function __toString()
    {
        if (array_key_exists('to_string', $this->configuration)) {
            $toStringProperty = $this->configuration['to_string'];

            if (isset($this->$toStringProperty)) {
                return $this->$toStringProperty;
            }
        }

        return gettype($this->values);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if ($value instanceof \Traversable || is_array($value)) {
            $newValue = [];
            foreach ($value as $key => $item) {
                $newValue[$key] = $this->context->getMetaForValue($item);
            }
            $this->$name = $newValue;

            return;
        }

        if ($this->context->isSupported($value)) {
            $this->$name = $this->context->getMetaForValue($value);

            return;
        }

        $this->$name = $value;
    }

    /**
     * @return array|object|string
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Fill Meta from array. Array must have property names and keys.
     *
     * @param array $values        Array with properyy names as keys
     * @param array $configuration
     *
     * @return bool
     */
    private function fillFromArray(array $values, $configuration)
    {
        foreach ($this->getExposedProperties($values, $configuration) as $key => $propertyValue) {
            $this->$key = $propertyValue;
        }

        return true;
    }

    /**
     * Fill Meta from object. Object must have public getters for properties.
     *
     * @param mixed $values        Object with public getters for properties
     * @param       $configuration
     *
     * @return bool
     */
    private function fillFromObject($values, $configuration)
    {
        foreach ($configuration['properties'] as $key => $propertyValue) {
            $getterName = 'get'.ucfirst($key);
            if (method_exists($values, $getterName)) {
                $this->$key = $values->$getterName();
            }
        }

        return true;
    }

    /**
     * Get exposed properties (according to configuration) from provided values.
     *
     * @param array $values
     * @param       $configuration
     *
     * @return array
     */
    private function getExposedProperties(array $values = [], $configuration = [])
    {
        $exposedProperties = [];
        if (count($values) > 0 && isset($configuration['properties'])) {
            foreach ($values as $key => $propertyValue) {
                if (array_key_exists($key, $configuration['properties'])) {
                    $exposedProperties[$key] = $propertyValue;
                }
            }
        }

        return $exposedProperties;
    }

    /**
     * Check if string is JSON.
     *
     * @param  string
     *
     * @return bool
     */
    private function isJson($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }
}
