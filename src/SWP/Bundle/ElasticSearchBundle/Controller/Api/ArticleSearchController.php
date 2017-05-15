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
use SWP\Bundle\ElasticSearchBundle\Repository\ArticleRepository;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
     *         {"name"="route", "dataType"="integer"},
     *         {"name"="publishedBefore", "dataType"="datetime", "pattern"="Y-m-d h:i:s|now-1M|now"},
     *         {"name"="publishedAfter", "dataType"="datetime", "pattern"="Y-m-d h:i:s"},
     *         {"name"="authors", "dataType"="string", "pattern"="John Doe | John Doe, Matt Smith"},
     *         {"name"="term", "dataType"="string", "pattern"="find that"},
     *         {"name"="sorting", "dataType"="string", "pattern"="-id|id"}
     *     }
     * )
     * @Route("/api/{version}/search/articles/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_search_articles")
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
                'route' => $request->query->get('route'),
                'status' => $request->query->get('status'),
                'authors' => array_filter(explode(',', $request->query->get('authors', ''))),
                'publishedBefore' => $request->query->get('publishedBefore'),
                'publishedAfter' => $request->query->get('publishedAfter'),
                'tenantCode' => $currentTenant->getCode(),
                'source' => $request->query->get('source'),
            ]
        );

        $repositoryManager = $this->get('fos_elastica.manager');
        /** @var ArticleRepository $repository */
        $repository = $repositoryManager->getRepository($this->getParameter('swp.model.article.class'));
        $articles = $repository->findByCriteria($criteria);
        $partialResult = $articles->getResults(
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
