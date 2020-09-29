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

use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
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
     * @Route("/api/{version}/packages/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_list_packages")
     */
    public function searchAction(Request $request, RepositoryManagerInterface $repositoryManager)
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

        $result = $repositoryManager
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
