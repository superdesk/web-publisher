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

use ArrayIterator;
use Countable;
use IteratorAggregate;

class Criteria implements IteratorAggregate, Countable
{
    protected $criteria;

    public function __construct(array $criteria = [])
    {
        $this->criteria = $criteria;
    }

    public function all(): array
    {
        return $this->criteria;
    }

    public function add(array $criteria = array()): void
    {
        $this->criteria = array_replace($this->criteria, $criteria);
    }

    public function get(string $key, $default = null)
    {
        return array_key_exists($key, $this->criteria) ? $this->criteria[$key] : $default;
    }

    public function set(string $key, $value): void
    {
        $this->criteria[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->criteria);
    }

    public function remove($key): void
    {
        unset($this->criteria[$key]);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->criteria);
    }

    public function count(): int
    {
        return count($this->criteria);
    }
}
