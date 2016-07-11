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

class Meta
{
    /**
     * Configuration definition for current Meta.
     *
     * @var array
     */
    protected $configuration;

    /**
     * Original Meta values (json|array).
     *
     * @var string|array
     */
    protected $values;

    /**
     * Create Meta class from provided configuration and values.
     *
     * @param array               $configuration
     * @param string|array|object $values
     */
    public function __construct(array $configuration, $values)
    {
        $this->configuration = array_slice($configuration, 0, 1);
        $this->configuration = array_shift($this->configuration);
        $this->values = $values;

        $this->fillMeta($values);
    }

    /**
     * Fill Meta from diffirent kind of data types.
     *
     * @param mixed $values
     *
     * @return bool
     */
    private function fillMeta($values)
    {
        if (is_array($values)) {
            return $this->fillFromArray($values);
        } elseif (is_string($values) && $this->isJson($values)) {
            return $this->fillFromJson($values);
        } elseif (is_object($values)) {
            return $this->fillFromObject($values);
        }

        return false;
    }

    /**
     * Fill Meta from array. Array must have property names and keys.
     *
     * @param array $values Array with propery names as keys
     *
     * @return bool
     */
    private function fillFromArray(array $values)
    {
        foreach ($this->getExposedProperties($values) as $key => $propertyValue) {
            $this->$key = $propertyValue;
        }

        return true;
    }

    /**
     * Fill Meta class from json values.
     *
     * @param string $values
     *
     * @return bool
     */
    private function fillFromJson($values)
    {
        $this->fillFromArray(json_decode($values, true));

        return true;
    }

    /**
     * Fill Meta from object. Object must have public getters for properties.
     *
     * @param object $values Object with public getters for properties
     *
     * @return bool
     */
    private function fillFromObject($values)
    {
        foreach ($this->configuration['properties'] as $key => $propertyValue) {
            $getterName = 'get'.ucfirst($key);
            $this->$key = $values->$getterName();
        }

        return true;
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

    /**
     * Get exposed properties (acording to configuration) from provided values.
     *
     * @return array
     */
    private function getExposedProperties(array $values = [])
    {
        if (count($values) === 0 && is_array($this->values)) {
            $values = $this->values;
        }

        $exposedProperties = [];
        if (count($values) > 0) {
            foreach ($values as $key => $propertyValue) {
                if (array_key_exists($key, $this->configuration['properties'])) {
                    $exposedProperties[$key] = $propertyValue;
                }
            }
        }

        return $exposedProperties;
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

        return json_encode($this->getExposedProperties());
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function getValues()
    {
        return $this->values;
    }
}
