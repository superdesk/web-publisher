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
namespace SWP\Component\TemplatesSystem\Gimme\Factory;

use SWP\Bundle\ContentBundle\Model\ImageRendition;
use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Meta\Meta;

class MetaFactory
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * MetaFactory constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function create($value, array $configuration = null)
    {
        if (null === $configuration) {
            $configuration = $this->context->getConfigurationForValue($value);
        }

        $meta = new Meta($this->context, $value, $configuration);

        if (is_array($value)) {
            $this->fillFromArray($value, $meta, $configuration);
        } elseif (is_string($value) && $this->isJson($value)) {
            $this->fillFromArray(json_decode($value, true), $meta, $configuration);
        } elseif (is_object($value)) {
            $this->fillFromObject($value, $meta, $configuration);
        }

        return $meta;
    }

    /**
     * Fill Meta from array. Array must have property names and keys.
     *
     * @param array $values Array with propery names as keys
     *
     * @return bool
     */
    private function fillFromArray(array $values, $meta, $configuration)
    {
        foreach ($this->getExposedProperties($values) as $key => $propertyValue) {
            $meta->$key = $propertyValue;
        }

        return true;
    }

    /**
     * Fill Meta from object. Object must have public getters for properties.
     *
     * @param object $values Object with public getters for properties
     *
     * @return bool
     */
    private function fillFromObject($values, $meta, $configuration)
    {
        foreach ($configuration['properties'] as $key => $propertyValue) {
            $getterName = 'get'.ucfirst($key);
            if (method_exists($values, $getterName)) {
                $meta->$key = $values->$getterName();
            }
        }

        return true;
    }

    /**
     * Get exposed properties (acording to configuration) from provided values.
     *
     * @return array
     */
    private function getExposedProperties(array $values = [])
    {
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