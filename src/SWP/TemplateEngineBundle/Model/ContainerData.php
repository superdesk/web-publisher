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
namespace SWP\TemplateEngineBundle\Model;

/**
 * ContainerData.
 */
class ContainerData
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param string $key
     * @param string $value
     */
    public function __construct($key, $value)
    {
        $this->setKey($key);
        $this->setValue($value);

        return $this;
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets the value of key.
     *
     * @param string $key the key
     *
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Gets the value of value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value of value.
     *
     * @param string $value the value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Gets the value of container.
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Sets the value of container.
     *
     * @param Container $container the container
     *
     * @return self
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    public function __toString()
    {
        return $this->getValue();
    }
}
