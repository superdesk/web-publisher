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
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Bundle\ElasticSearchBundle\Repository\PackageRepository;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     *         {"name"="published_before", "dataType"="datetime", "pattern"="Y-m-d h:i:s|now-1M|now", "description"="The datetime before which the package has been published"},
     *         {"name"="published_after", "dataType"="datetime", "pattern"="Y-m-d h:i:s|now-1M|now", "description"="The datetime after which the package has been published"},
     *         {"name"="author", "dataType"="array", "description"="Package authors"},
     *         {"name"="term", "dataType"="string", "pattern"="search phrase", "description"="Search phrase"},
     *         {"name"="sorting", "dataType"="array", "pattern"="sorting[id]=desc", "description"="List order"},
     *         {"name"="source", "dataType"="array", "description"="Sources"},
     *         {"name"="limit", "dataType"="integer", "description"="Items per page"},
     *         {"name"="page", "dataType"="integer", "description"="Page number"},
     *         {"name"="tenant", "dataType"="array", "description"="Tenant codes"},
     *         {"name"="route", "dataType"="array", "description"="Routes ids"}
     *     }
     * )
     * @Route("/api/{version}/packages/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_list_packages")
     */
    public function searchAction(Request $request)
    {
        $this->get('event_dispatcher')->dispatch(MultiTenancyEvents::TENANTABLE_DISABLE);
        $currentTenant = $this->get('swp_multi_tenancy.tenant_context')->getTenant();

        $criteria = Criteria::fromQueryParameters(
            $request->query->get('term', ''),
            [
                'page' => $request->query->get('page'),
                'sort' => $request->query->get('sorting'),
                'limit' => $request->query->get('limit', 10),
                'authors' => array_filter((array) $request->query->get('author', [])),
                'publishedBefore' => $request->query->get('published_before'),
                'publishedAfter' => $request->query->get('published_after'),
                'organization' => $currentTenant->getOrganization()->getId(),
                'sources' => array_filter((array) $request->query->get('source', [])),
                'tenants' => array_filter((array) $request->query->get('tenant', [])),
                'routes' => array_filter((array) $request->query->get('route', [])),
                'statuses' => array_filter((array) $request->query->get('status', [])),
            ]
        );

        $repositoryManager = $this->get('fos_elastica.manager');
        /** @var PackageRepository $repository */
        $repository = $repositoryManager->getRepository($this->getParameter('swp.model.package.class'));
        $result = $repository->findByCriteria($criteria);
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $result,
            $request->query->get('page', 1),
            $criteria->getPagination()->getItemsPerPage()
        );

        return new ResourcesListResponse($pagination);
    }
}
