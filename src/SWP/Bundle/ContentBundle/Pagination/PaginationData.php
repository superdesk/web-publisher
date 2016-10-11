<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Pagination;

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

    public function getFirstResult(): int
    {
        if ($this->getPageNumber() > 1) {
            return $this->getPageNumber() * $this->getLimit();
        }

        return 0;
    }

    public function resolveFromRequest(Request $request)
    {
        $this->setPageNumber((int) $request->get(self::PAGE_PARAMETER_NAME, 1));
        $this->setLimit((int) $request->get(self::LIMIT_PARAMETER_NAME, 10));
    }
}
