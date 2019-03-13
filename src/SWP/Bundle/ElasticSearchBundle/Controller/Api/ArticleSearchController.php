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
use SWP\Bundle\ElasticSearchBundle\Repository\ArticleRepository;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleSearchController extends Controller
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Search articles",
     *     statusCodes={
     *         200="Returned on success.",
     *         500="Unexpected error."
     *     },
     *     filters={
     *         {"name"="status", "dataType"="array", "pattern"="new|published|unpublished|canceled","description"="Package status"},
     *         {"name"="publishedBefore", "dataType"="datetime", "pattern"="Y-m-d h:i:s|now|d/m/Y", "description"="The datetime before which the article has been published"},
     *         {"name"="publishedAfter", "dataType"="datetime", "pattern"="Y-m-d h:i:s|now|d/m/Y", "description"="The datetime after which the article has been published"},
     *         {"name"="author", "dataType"="array", "description"="Article authors"},
     *         {"name"="term", "dataType"="string", "pattern"="search phrase", "description"="Search phrase"},
     *         {"name"="sorting", "dataType"="array", "pattern"="sorting[id]=desc", "description"="List order"},
     *         {"name"="source", "dataType"="array", "description"="Sources"},
     *         {"name"="limit", "dataType"="integer", "description"="Items per page"},
     *         {"name"="page", "dataType"="integer", "description"="Page number"},
     *         {"name"="route", "dataType"="array", "description"="Routes ids"},
     *         {"name"="metadata", "dataType"="array", "description"="Metadata (e.g. query param: ?metadata[located][]=Sydney&metadata[located][]=Berlin)"}
     *     }
     * )
     * @Route("/api/{version}/content/articles/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_content_list_articles")
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
                'routes' => array_filter((array) $request->query->get('route', [])),
                'statuses' => array_filter((array) $request->query->get('status', [])),
                'authors' => array_filter((array) $request->query->get('author', [])),
                'publishedBefore' => $request->query->has('publishedBefore') ? new \DateTime($request->query->get('publishedBefore')) : null,
                'publishedAfter' => $request->query->has('publishedAfter') ? new \DateTime($request->query->get('publishedAfter')) : null,
                'publishedAt' => $request->query->get('publishedAt'),
                'tenantCode' => $currentTenant->getCode(),
                'sources' => array_filter((array) $request->query->get('source', [])),
                'metadata' => array_filter((array) $request->query->get('metadata', [])),
            ]
        );

        $extraFields = $this->get('service_container')->getParameter('env(ELASTICA_ARTICLE_EXTRA_FIELDS)');

        $repositoryManager = $this->get('fos_elastica.manager');
        /** @var ArticleRepository $repository */
        $repository = $repositoryManager->getRepository($this->getParameter('swp.model.article.class'));
        $articles = $repository->findByCriteria($criteria, json_decode($extraFields, true));
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            $criteria->getPagination()->getItemsPerPage()
        );

        return new ResourcesListResponse($pagination);
    }
}
