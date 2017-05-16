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

namespace SWP\Bundle\ElasticSearchBundle\Controller\Api;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Bundle\ElasticSearchBundle\Repository\PackageRepository;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PackageSearchController extends Controller
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="List all packages",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Returned when unexpected error."
     *     },
     *     filters={
     *         {"name"="status", "dataType"="array", "pattern"="new|published|unpublished|canceled","description"="Package status"},
     *         {"name"="publishedBefore", "dataType"="datetime", "pattern"="Y-m-d h:i:s|now-1M|now", "description"="The datetime before which the package has been published"},
     *         {"name"="publishedAfter", "dataType"="datetime", "pattern"="Y-m-d h:i:s|now-1M|now", "description"="The datetime after which the package has been published"},
     *         {"name"="authors", "dataType"="string", "pattern"="John Doe | John Doe, Matt Smith", "description"="Package authors"},
     *         {"name"="term", "dataType"="string", "pattern"="search phrase", "description"="Search phrase"},
     *         {"name"="sorting", "dataType"="array", "pattern"="sorting[id]=desc", "description"="List order"},
     *         {"name"="source", "dataType"="string", "description"="Package source"},
     *         {"name"="limit", "dataType"="integer", "description"="Items per page"},
     *         {"name"="page", "dataType"="integer", "description"="Page number"},
     *         {"name"="tenant", "dataType"="string", "description"="Tenant's code"}
     *     }
     * )
     * @Route("/api/{version}/packages/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_core_list_packages")
     * @Method("GET")
     */
    public function searchAction(Request $request)
    {
        $currentTenant = $this->get('swp_multi_tenancy.tenant_context')->getTenant();

        $criteria = Criteria::fromQueryParameters(
            $request->query->get('term', ''),
            [
                'page' => $request->query->get('page'),
                'sort' => $request->query->get('sorting'),
                'limit' => $request->query->get('limit', 10),
                'authors' => array_filter(explode(',', $request->query->get('authors', ''))),
                'publishedBefore' => $request->query->get('publishedBefore'),
                'publishedAfter' => $request->query->get('publishedAfter'),
                'organization' => $currentTenant->getOrganization()->getId(),
                'source' => $request->query->get('source'),
                'tenantCode' => $request->query->get('tenant'),
                'route' => $request->query->get('route'),
                'status' => array_filter((array) $request->query->get('status', [])),
            ]
        );

        $repositoryManager = $this->get('fos_elastica.manager');
        /** @var PackageRepository $repository */
        $repository = $repositoryManager->getRepository($this->getParameter('swp.model.package.class'));
        $result = $repository->findByCriteria($criteria);

        $partialResult = $result->getResults(
            $criteria->getPagination()->getOffset(),
            $criteria->getPagination()->getItemsPerPage()
        );

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $partialResult->toArray(),
            $request->query->get('page', 1),
            $criteria->getPagination()->getItemsPerPage()
        );

        return new ResourcesListResponse($pagination);
    }
}
