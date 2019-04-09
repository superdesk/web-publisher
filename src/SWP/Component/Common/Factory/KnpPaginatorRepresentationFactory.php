<?php

/*
 * This file is part of the Superdesk Web Publisher Common Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Common\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Hateoas\Configuration\Route;
use Hateoas\Representation\PaginatedRepresentation;
use Knp\Component\Pager\Pagination\AbstractPagination;
use SWP\Component\Common\Pagination\PaginationInterface;
use SWP\Component\Common\Representation\CollectionRepresentation;
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
     *
     * @return PaginatedRepresentation
     */
    public function createRepresentation(AbstractPagination $pagination, Request $request)
    {
        $route = new Route($request->get('_route', 'homepage'), array_merge($request->get('_route_params', []), $request->query->all()));

        $routeParameters = is_array($route->getParameters()) ? $route->getParameters() : [];

        $numberOfPages = 1;
        if ($pagination->getTotalItemCount() > 0 && $pagination->getItemNumberPerPage() > 0) {
            $numberOfPages = (int) ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage());
        }

        $items = $pagination->getItems();
        switch (true) {
            case $items instanceof ArrayCollection:
                $items = $items->toArray();

                break;
            case $items instanceof \ArrayObject:
                $items = $items->getArrayCopy();

                break;
        }

        return new PaginatedRepresentation(
            new CollectionRepresentation(array_values($items)),
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
