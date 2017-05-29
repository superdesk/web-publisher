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
    private $pagination;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Filters
     */
    private $filters;

    /**
     * @param string     $term
     * @param Pagination $pagination
     * @param Order      $order
     * @param Filters    $filters
     */
    private function __construct(string $term, Pagination $pagination, Order $order, Filters $filters)
    {
        $this->term = $term;
        $this->pagination = $pagination;
        $this->order = $order;
        $this->filters = $filters;
    }

    /**
     * @param $term
     * @param array $parameters
     *
     * @return Criteria
     */
    public static function fromQueryParameters($term, array $parameters)
    {
        $pagination = Pagination::fromQueryParameters($parameters);
        $order = Order::fromQueryParameters($parameters);
        $filters = Filters::fromQueryParameters($parameters);

        return new self($term, $pagination, $order, $filters);
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
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return Filters
     */
    public function getFilters()
    {
        return $this->filters;
    }
}
