<?php

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

namespace SWP\Bundle\ContentBundle\Factory;

use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Knp\Component\Pager\Pagination\AbstractPagination;
use SWP\Bundle\ContentBundle\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;

class KnpPaginatorRepresentationFactory
{
    /**
     * @var string
     */
    private $pageParameterName;

    /**
     * @var string
     */
    private $limitParameterName;

    /**
     * @param string $pageParameterName
     * @param string $limitParameterName
     */
    public function __construct($pageParameterName = PaginationInterface::PAGE_PARAMETER_NAME, $limitParameterName = PaginationInterface::LIMIT_PARAMETER_NAME)
    {
        $this->pageParameterName = $pageParameterName;
        $this->limitParameterName = $limitParameterName;
    }

    /**
     * @param AbstractPagination $pagination
     * @param Request            $request
     * @param string             $collectionName
     *
     * @return PaginatedRepresentation
     */
    public function createRepresentation(AbstractPagination $pagination, Request $request, $collectionName = '_items')
    {
        $route = new Route($request->get('_route'), $request->query->all());
        $routeParameters = is_array($route->getParameters()) ? $route->getParameters() : [];
        $numberOfPages = 1;
        if ($pagination->getTotalItemCount() > 0 && $pagination->getItemNumberPerPage() > 0) {
            $numberOfPages = intval(ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()));
        }

        return new PaginatedRepresentation(
            new CollectionRepresentation($pagination->getItems(), $collectionName),
            $route->getName(),
            $routeParameters,
            $pagination->getCurrentPageNumber(),
            $pagination->getItemNumberPerPage(),
            $numberOfPages,
            $this->getPageParameterName(),
            $this->getLimitParameterName(),
            $route->isAbsolute(),
            $pagination->getTotalItemCount()
        );
    }

    /**
     * @return string
     */
    public function getPageParameterName()
    {
        return $this->pageParameterName;
    }

    /**
     * @return string
     */
    public function getLimitParameterName()
    {
        return $this->limitParameterName;
    }
}
