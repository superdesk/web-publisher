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
use SWP\Bundle\CoreBundle\Model\Package;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PackageSearchController extends Controller
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Search packages",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Returned when unexpected error."
     *     },
     *     filters={
     *         {"name"="status", "dataType"="string", "pattern"="new|published|unpublished|canceled"},
     *         {"name"="publishedBefore", "dataType"="datetime", "pattern"="Y-m-d h:i:s|now-1M|now"},
     *         {"name"="publishedAfter", "dataType"="datetime", "pattern"="Y-m-d h:i:s|now-1M|now"},
     *         {"name"="authors", "dataType"="string", "pattern"="John Doe | John Doe, Matt Smith"},
     *         {"name"="term", "dataType"="string", "pattern"="search phrase"},
     *         {"name"="sorting", "dataType"="string", "pattern"="-id|id"},
     *         {"name"="source", "dataType"="string"}
     *     }
     * )
     * @Route("/api/{version}/search/packages/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_search_packages")
     * @Method("GET")
     */
    public function searchAction(Request $request)
    {
        $currentTenant = $this->get('swp_multi_tenancy.tenant_context')->getTenant();

        $criteria = Criteria::fromQueryParameters(
            $request->query->get('term', ''),
            [
                'sort' => $request->query->get('sorting'),
                'per_page' => $request->query->get('per_page', 10),
                'authors' => explode(',', $request->query->get('authors', [])),
                'publishedBefore' => $request->query->get('publishedBefore'),
                'publishedAfter' => $request->query->get('publishedAfter'),
                'organization' => $currentTenant->getOrganization()->getId(),
                'source' => $request->query->get('source'),
            ]
        );

        $repositoryManager = $this->get('fos_elastica.manager');
        $repository = $repositoryManager->getRepository(Package::class);

        $result = $repository->findByCriteria($criteria);

        $partialResult = $result->getResults(
            $criteria->getPaginating()->getOffset(),
            $criteria->getPaginating()->getItemsPerPage()
        );

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $partialResult->toArray(),
            $request->query->get('page', 1),
            $criteria->getPaginating()->getItemsPerPage()
        );

        return new ResourcesListResponse($pagination);
    }
}
