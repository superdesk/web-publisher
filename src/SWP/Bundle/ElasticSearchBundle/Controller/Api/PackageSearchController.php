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

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PackageSearchController extends Controller
{
    /**
     * @Operation(
     *     tags={"package"},
     *     summary="List all packages",
     *     @SWG\Parameter(
     *         name="status",
     *         in="query",
     *         description="Package status",
     *         required=false,
     *         type="array",
     *         @SWG\Items(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="published_before",
     *         in="query",
     *         description="The datetime before which the package has been published",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="published_after",
     *         in="query",
     *         description="The datetime after which the package has been published",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="author",
     *         in="query",
     *         description="Package authors",
     *         required=false,
     *         type="array",
     *         @SWG\Items(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="term",
     *         in="query",
     *         description="Search phrase",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="sorting",
     *         in="query",
     *         description="List order",
     *         required=false,
     *         type="array",
     *         @SWG\Items(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="source",
     *         in="query",
     *         description="Sources",
     *         required=false,
     *         type="array",
     *         @SWG\Items(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="tenant",
     *         in="query",
     *         description="Tenant codes",
     *         required=false,
     *         type="array",
     *         @SWG\Items(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language code, e.g. en",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="route",
     *         in="query",
     *         description="Routes ids",
     *         required=false,
     *         type="array",
     *         @SWG\Items(type="integer")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\Package::class, groups={"api"}))
     *         )
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Returned when unexpected error."
     *     )
     * )
     *
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
                'language' => $request->query->get('language', ''),
            ]
        );

        $result = $this->get('fos_elastica.manager')
            ->getRepository($this->getParameter('swp.model.package.class'))
            ->findByCriteria($criteria);

        $pagination = $this->get('knp_paginator')->paginate(
            $result,
            $request->query->get('page', 1),
            $criteria->getPagination()->getItemsPerPage()
        );

        $responseContext = new ResponseContext();
        $responseContext->setSerializationGroups(
            [
                'Default',
                'api_packages_list',
                'api_packages_items_list',
                'api_tenant_list',
                'api_articles_list',
                'api_articles_slideshows',
                'api_articles_featuremedia',
                'api_articles_statistics_list',
                'api_article_authors',
                'api_article_media_list',
                'api_article_media_renditions',
                'api_image_details',
                'api_groups_list',
                'api_routes_list',
            ]
        );

        return new ResourcesListResponse($pagination, $responseContext);
    }
}
