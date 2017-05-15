<?php

declare(strict_types=1);
/*
 * This file is part of the Superdesk Web Publisher ElasticSearch Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ElasticSearchBundle\Criteria;

final class Criteria
{
    /**
     * @var string
     */
    private $term;

    /**
     * @var Pagination
     */
    private $paginating;

    /**
     * @var Ordering
     */
    private $ordering;

    /**
     * @var Filtering
     */
    private $filtering;

    /**
     * @param string     $term
     * @param Pagination $paginating
     * @param Ordering   $ordering
     * @param Filtering  $filtering
     */
    private function __construct(string $term, Pagination $paginating, Ordering $ordering, Filtering $filtering)
    {
        $this->term = $term;
        $this->paginating = $paginating;
        $this->ordering = $ordering;
        $this->filtering = $filtering;
    }

    /**
     * @param $term
     * @param array $parameters
     *
     * @return Criteria
     */
    public static function fromQueryParameters($term, array $parameters)
    {
        $paginating = Pagination::fromQueryParameters($parameters);
        $ordering = Ordering::fromQueryParameters($parameters);
        $filtering = Filtering::fromQueryParameters($parameters);

        return new self($term, $paginating, $ordering, $filtering);
    }

    /**
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @return Pagination
     */
    public function getPaginating()
    {
        return $this->paginating;
    }

    /**
     * @return Ordering
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * @return Filtering
     */
    public function getFiltering()
    {
        return $this->filtering;
    }
}
