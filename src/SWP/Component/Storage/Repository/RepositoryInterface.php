<?php

/*
 * This file is part of the Superdesk Web Publisher Storage Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Storage\Repository;

use Doctrine\Persistence\ObjectRepository;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Storage\Model\PersistableInterface;
use Doctrine\ORM\QueryBuilder;

interface RepositoryInterface extends ObjectRepository
{
    /**
     * Default value for number of results.
     */
    const MAX_RESULTS = 10;

    /**
     * Persists new object in manager.
     *
     * @param PersistableInterface $object
     *
     * @return mixed
     */
    public function persist(PersistableInterface $object);

    /**
     * Adds new object into the repository.
     *
     * @param PersistableInterface $object
     */
    public function add(PersistableInterface $object);

    /**
     * Flush persisted and managed objects.
     */
    public function flush();

    /**
     * Removes an object from the repository.
     *
     * @param PersistableInterface $object
     */
    public function remove(PersistableInterface $object);

    /**
     * @param Criteria            $criteria
     * @param array               $sorting
     * @param PaginationData|null $paginationData
     *
     * @return mixed
     */
    public function getPaginatedByCriteria(Criteria $criteria, array $sorting = [], PaginationData $paginationData = null);

    /**
     * @param Criteria $criteria
     * @param array    $sorting
     * @param string   $alias
     *
     * @return QueryBuilder
     */
    public function getQueryByCriteria(Criteria $criteria, array $sorting, string $alias): QueryBuilder;
}
