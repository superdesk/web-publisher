<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\ContentBundle\Search;

use Symfony\Component\HttpFoundation\ParameterBag;
use InvalidArgumentException;

/**
 * Search criteria object used in \SWP\ContentBundle\StorageInterface
 * searchDocuments method.
 */
class SearchCriteria extends ParameterBag implements SearchCriteriaInterface
{
    /**
     * Predefined options for searching
     *
     * @var array
     */
    protected $options;

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->getValueOrNull($this->options, 'limit');
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($limit)
    {
        if (!is_null($limit) && !is_int($limit)) {
            throw new InvalidArgumentException('Invalid datatype for limit.');
        }

        $this->options['limit'] = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset()
    {
        return $this->getValueOrNull($this->options, 'offset');
    }

    /**
     * {@inheritdoc}
     */
    public function setOffset($offset)
    {
        if (!is_null($offset) && !is_int($offset)) {
            throw new InvalidArgumentException('Invalid datatype for offset.');
        }

        $this->options['offset'] = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderBy()
    {
        return $this->getValueOrNull($this->options, 'orderby');
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderBy($orderby)
    {
        if (!is_null($orderby)) {
            $checkType = (!is_array($orderby)) ? array($orderby) : $orderby;
            foreach($checkType as $orderbyStatement) {
                if (!is_sring($orderbyStatement)) {
                    throw new InvalidArgumentException('Invalid datatype for orderby. Should be null, string or array containing only strings.');
                }
            }
        }

        $this->options['orderby'] = $limit;
    }

    /**
     * Returns value of array if set, else null.
     *
     * @param  array $array
     * @param  string $key
     *
     * @return mixed|null
     */
    private function getValueOrNull($array, $key)
    {
        return (isset($array[$key])) ? $array[$key] : null;
    }
}
