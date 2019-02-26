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

namespace SWP\Component\TemplatesSystem\Gimme\Meta;

use SWP\Component\TemplatesSystem\Gimme\Context\Context;
use SWP\Component\TemplatesSystem\Gimme\Event\MetaEvent;

/**
 * Class Meta.
 */
class Meta implements MetaInterface
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
     * @var array
     */
    private $copiedValues = [];

    public function __construct(Context $context, $values, $configuration)
    {
        $this->context = $context;
        $this->values = $values;
        $this->configuration = $configuration;

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
            $this->__load($toStringProperty);
            if (isset($this->copiedValues[$toStringProperty])) {
                return $this->copiedValues[$toStringProperty];
            }
        }

        return gettype($this->values);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name)
    {
        $this->__load($name);
        if (array_key_exists($name, $this->copiedValues)) {
            return true;
        }

        return false;
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
                $newValue[$key] = $this->getValueOrMeta($item);
            }

            $this->copiedValues[$name] = $newValue;

            return;
        }

        $this->copiedValues[$name] = $this->getValueOrMeta($value);
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->copiedValues)) {
            return $this->copiedValues[$name];
        }
        $this->__load($name);

        return $this->copiedValues[$name];
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
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Don't serialize values, context and configuration.
     *
     * @return array
     */
    public function __sleep()
    {
        unset($this->values);
        unset($this->context);
        unset($this->configuration);

        return array_keys(get_object_vars($this));
    }

    /**
     * @param array  $values
     * @param array  $configuration
     * @param string $name
     *
     * @return bool
     */
    private function fillFromArray(array $values, array $configuration, string $name)
    {
        if (count($values) > 0 && isset($configuration['properties'][$name]) && isset($values[$name])) {
            $this->$name = $values[$name];
        }

        return true;
    }

    private function fillFromObject($values, array $configuration, string $name): bool
    {
        if (isset($configuration['properties'][$name])) {
            $type = $configuration['properties'][$name]['type'];

            $event = new MetaEvent($this->getValues(), $name);
            $this->context->dispatchMetaEvent($event);
            if ($event->isResultSet()) {
                $this->$name = $event->getResult();

                return true;
            }

            $getterName = 'get'.ucfirst($name);

            if ('bool' === $type) {
                $getterName = 'is'.ucfirst($name);
            }

            if (method_exists($values, $getterName)) {
                $this->$name = $values->$getterName();
            }
        }

        return true;
    }

    private function isJson(string $string): bool
    {
        json_decode($string);

        return JSON_ERROR_NONE === json_last_error();
    }

    /**
     * @param mixed $value
     *
     * @return mixed|Meta
     */
    private function getValueOrMeta($value)
    {
        if ($this->context->isSupported($value)) {
            return $this->context->getMetaForValue($value);
        }

        return $value;
    }

    /**
     * @param string $name
     */
    private function __load(string $name)
    {
        if (is_array($this->values)) {
            $this->fillFromArray($this->values, $this->configuration, $name);
        } elseif (is_string($this->values) && $this->isJson($this->values)) {
            $this->fillFromArray(json_decode($this->values, true), $this->configuration, $name);
        } elseif (is_object($this->values)) {
            $this->fillFromObject($this->values, $this->configuration, $name);
        }
    }
}
