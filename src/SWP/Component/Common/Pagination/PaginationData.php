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

namespace SWP\Component\Common\Pagination;

use Symfony\Component\HttpFoundation\Request;

class PaginationData implements PaginationInterface
{
    /**
     * @var int
     */
    protected $pageNumber = 1;

    /**
     * @var int
     */
    protected $limit = 10;

    /**
     * @var int
     */
    protected $firstResult = 0;

    /**
     * @var string
     */
    protected $orderDirection = 'asc';

    /**
     * @var array
     */
    protected $orderFields = [];

    /**
     * @var array
     */
    protected $orderAliases = [];

    /**
     * PaginationData constructor.
     *
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        if (null !== $request) {
            $this->resolveFromRequest($request);
        }
    }

    /**
     * @return int
     */
    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * @param int $pageNumber
     */
    public function setPageNumber(int $pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getFirstResult(): int
    {
        if ($this->getPageNumber() > 1) {
            return $this->getPageNumber() * $this->getLimit();
        }

        return $this->firstResult;
    }

    /**
     * @param int $firstResult
     */
    public function setFirstResult(int $firstResult)
    {
        $this->firstResult = $firstResult;
    }

    /**
     * @param array $order
     */
    public function setOrder(array $order)
    {
        if (2 === count($order) && in_array(strtolower($order[1]), ['asc', 'desc'])) {
            $this->orderDirection = $order[1];
            $fields = array();
            $aliases = array();
            foreach (explode('+', $order[0]) as $sortFieldParameterName) {
                $parts = explode('.', $sortFieldParameterName, 2);
                // We have to prepend the field. Otherwise OrderByWalker will add
                // the order-by items in the wrong order
                array_unshift($fields, end($parts));
                array_unshift($aliases, 2 <= count($parts) ? reset($parts) : false);
            }
            $this->orderFields = $fields;
            $this->orderAliases = $aliases;
        }
    }

    /**
     * @return string
     */
    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    /**
     * @return array
     */
    public function getOrderFields(): array
    {
        return $this->orderFields;
    }

    /**
     * @return array
     */
    public function getOrderAliases(): array
    {
        return $this->orderAliases;
    }

    /**
     * @param Request $request
     */
    public function resolveFromRequest(Request $request)
    {
        $this->setPageNumber((int) $request->get(PaginationInterface::PAGE_PARAMETER_NAME, 1));
        $this->setLimit((int) $request->get(PaginationInterface::LIMIT_PARAMETER_NAME, 10));
    }
}
