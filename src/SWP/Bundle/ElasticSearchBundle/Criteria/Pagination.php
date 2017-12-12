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

final class Pagination
{
    const DEFAULT_CURRENT_PAGE = 1;

    const DEFAULT_ITEMS_PER_PAGE = 10;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * @var int
     */
    private $itemsPerPage;

    /**
     * @var int
     */
    private $offset;

    /**
     * @param int $currentPage
     * @param int $itemsPerPage
     */
    private function __construct($currentPage, $itemsPerPage)
    {
        $this->currentPage = (int) $currentPage;
        if (0 >= $currentPage) {
            $this->currentPage = self::DEFAULT_CURRENT_PAGE;
        }
        $this->itemsPerPage = (int) $itemsPerPage;
        if (0 >= $itemsPerPage) {
            $this->itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE;
        }
        $this->offset = $this->currentPage * $this->itemsPerPage - $this->itemsPerPage;
    }

    /**
     * @param array $parameters
     *
     * @return Pagination
     */
    public static function fromQueryParameters(array $parameters)
    {
        $currentPage = isset($parameters['page']) ? $parameters['page'] : self::DEFAULT_CURRENT_PAGE;
        $itemsPerPage = isset($parameters['limit']) ? $parameters['limit'] : self::DEFAULT_ITEMS_PER_PAGE;

        return new self($currentPage, $itemsPerPage);
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }
}
