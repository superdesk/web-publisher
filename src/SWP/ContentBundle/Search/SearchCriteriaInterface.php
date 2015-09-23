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

/**
 * Defines extra options that are used in
 * \SWP\ContentBundle\Storage\StorageInterface searchDocuments method.
 */
interface SearchCriteriaInterface
{
    /**
     * Get limit for search query.
     *
     * @return int|null
     */
    public function getLimit();

    /**
     * Set limit for search.
     *
     * @param int|null $limit
     *
     * @throws \InvalidArgumentException
     */
    public function setLimit($limit);

    /**
     * Get offset for search query.
     *
     * @return int|null
     */
    public function getOffset();

    /**
     * Set offset for search.
     *
     * @param int|null $offset
     *
     * @throws \InvalidArgumentException
     */
    public function setOffset($offset);

    /**
     * Get order for search query.
     *
     * @return string|string[]|null
     */
    public function getOrderBy();

    /**
     * Set order for search.
     *
     * @param string|string[]|null $orderby
     *
     * @throws \InvalidArgumentException
     */
    public function setOrderBy($orderby);

    /**
     * Returns all criteria
     *
     * @return array
     */
    public function all();

    /**
     * Sets criteria key value pair
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);
}
