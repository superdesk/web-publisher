<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Filter;

use SWP\Bundle\CoreBundle\Filter\Exception\KeyNotExistsException;
use SWP\Bundle\CoreBundle\Filter\Exception\NotEqualDataException;

class DataFilter
{
    protected $currentData;

    protected $initialData;

    public function loadData(array $data): DataFilter
    {
        $this->initialData = $data;
        $this->currentData = $data;

        return $this;
    }

    public function reset(): DataFilter
    {
        $this->currentData = $this->initialData;
    }

    public function contains(string $key): DataFilter
    {
        if (!array_key_exists($key, $this->currentData)) {
            throw new KeyNotExistsException($key, \array_keys($this->currentData));
        }

        $this->currentData = $this->currentData[$key];

        return $this;
    }

    public function containsItem(string $key, $value): DataFilter
    {
        $found = false;
        foreach ($this->currentData as $item) {
            if (array_key_exists($key, $item) && $item[$key] === $value) {
                $found = true;
            }
        }

        if (!$found) {
            throw new NotEqualDataException($key, $value);
        }

        return $this;
    }
}
