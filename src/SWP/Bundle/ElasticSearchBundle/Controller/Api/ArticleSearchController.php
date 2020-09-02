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

use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Bundle\ElasticSearchBundle\Repository\ArticleRepository;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleSearchController extends Controller
{
    /**
     * @Route("/api/{version}/content/articles/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_list_articles")
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
                'publishedBefore' => $request->query->has('published_before') ? new \DateTime($request->query->get('published_before')) : null,
                'publishedAfter' => $request->query->has('published_after') ? new \DateTime($request->query->get('published_after')) : null,
                'publishedAt' => $request->query->get('published_at'),
                'tenantCode' => $currentTenant->getCode(),
                'sources' => array_filter((array) $request->query->get('source', [])),
                'metadata' => array_filter((array) $request->query->get('metadata', [])),
                'keywords' => array_filter((array) $request->query->get('keywords', [])),
            ]
        );

        $extraFields = $this->get('service_container')->getParameter('env(ELASTICA_ARTICLE_EXTRA_FIELDS)');

        $options = [
            'sortNestedPath' => 'articleStatistics.pageViewsNumber',
        ];

        $repositoryManager = $this->get('fos_elastica.manager');
        /** @var ArticleRepository $repository */
        $repository = $repositoryManager->getRepository($this->getParameter('swp.model.article.class'));
        $articles = $repository->findByCriteria($criteria, json_decode($extraFields, true));
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            $criteria->getPagination()->getItemsPerPage(),
            $options
        );

        $responseContext = new ResponseContext();
        $responseContext->setSerializationGroups(
            [
                'Default',
                'api_articles_list',
                'api_articles_featuremedia',
                'api_article_authors',
                'api_article_media_list',
                'api_article_media_renditions',
                'api_image_details',
                'api_routes_list',
                'api_tenant_list',
                'api_articles_statistics_list',
            ]
        );

        return new ResourcesListResponse($pagination, $responseContext);
    }
}
