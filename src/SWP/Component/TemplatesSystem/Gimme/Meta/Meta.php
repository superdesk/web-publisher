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

class Meta
{

    /**
     * Original Meta values (json|array).
     *
     * @var string|array
     */
    protected $values;

    /**
     * @var Context
     */
    protected $context;

    /**
     * Create Meta class from provided configuration and values.
     *
     * @param Context             $context
     * @param string|array|object $values
     * @param array               $configuration
     */
    public function __construct(Context $context, $values)
    {
        $this->context = $context;
        $this->values = $values;
    }

    /**
     * Use to_string property from configuration if provided, json with exposed properties otherwise.
     *
     * @return string
     */
    public function __toString()
    {
        $configuration = $this->context->getConfigurationForValue($this->values);
        if (array_key_exists('to_string', $configuration)) {
            $toStringProperty = $configuration['to_string'];

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

}
