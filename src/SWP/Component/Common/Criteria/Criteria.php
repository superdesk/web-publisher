<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Criteria;

class Criteria implements \IteratorAggregate, \Countable
{
    /**
     * Criteria storage.
     *
     * @var array
     */
    protected $criteria;

    /**
     * Constructor.
     *
     * @param array $criteria An array of criteria
     */
    public function __construct(array $criteria = [])
    {
        $this->criteria = $criteria;
    }

    /**
     * Returns the criteria.
     *
     * @return array An array of criteria
     */
    public function all()
    {
        return $this->criteria;
    }

    /**
     * Adds criteria.
     *
     * @param array $criteria An array of criteria
     */
    public function add(array $criteria = array())
    {
        $this->criteria = array_replace($this->criteria, $criteria);
    }

    /**
     * Returns a parameter by name.
     *
     * @param string $key     The key
     * @param mixed  $default The default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->criteria) ? $this->criteria[$key] : $default;
    }

    /**
     * Sets a parameter by name.
     *
     * @param string $key   The key
     * @param mixed  $value The value
     */
    public function set($key, $value)
    {
        $this->criteria[$key] = $value;
    }

    /**
     * Returns true if the parameter is defined.
     *
     * @param string $key The key
     *
     * @return bool true if the parameter exists, false otherwise
     */
    public function has($key)
    {
        return array_key_exists($key, $this->criteria);
    }

    /**
     * Removes a parameter.
     *
     * @param string $key The key
     */
    public function remove($key)
    {
        unset($this->criteria[$key]);
    }

    /**
     * Returns an iterator for criteria.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->criteria);
    }

    /**
     * Returns the number of criteria.
     *
     * @return int The number of criteria
     */
    public function count()
    {
        return count($this->criteria);
    }
}
