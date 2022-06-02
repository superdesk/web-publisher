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
use Knp\Component\Pager\PaginatorInterface;
use SWP\Bundle\CoreBundle\Context\CachedTenantContext;
use SWP\Bundle\ElasticSearchBundle\Criteria\Criteria;
use SWP\Bundle\ElasticSearchBundle\Repository\ArticleRepository;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResponseContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleSearchController extends AbstractController {
  private $extraFields; //
  private CachedTenantContext $cachedTenantContext; // swp_multi_tenancy.tenant_context
  private PaginatorInterface $paginator; // knp_paginator

  /**
   * @param $extraFields
   * @param CachedTenantContext $cachedTenantContext
   * @param PaginatorInterface $paginator
   */
  public function __construct($extraFields, CachedTenantContext $cachedTenantContext, PaginatorInterface $paginator) {
    $this->extraFields = $extraFields;
    $this->cachedTenantContext = $cachedTenantContext;
    $this->paginator = $paginator;
  }

  /**
   * @Route("/api/{version}/content/articles/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_content_list_articles")
   */
  public function searchAction(Request $request, RepositoryManagerInterface $repositoryManager) {
    $criteria = $this->createCriteriaFrom($request);
    $extraFields = $this->extraFields;

    $options = [
        'sortNestedPath' => 'articleStatistics.pageViewsNumber',
    ];

    /** @var ArticleRepository $repository */
    $repository = $repositoryManager->getRepository($this->getParameter('swp.model.article.class'));
    $articles = $repository->findByCriteria($criteria, json_decode($extraFields, true));
    $paginator = $this->paginator;
    $pagination = $paginator->paginate(
        $articles,
        $request->query->get('page', 1),
        $criteria->getPagination()->getItemsPerPage(),
        $options
    );

    $responseContext = new ResponseContext();
    $responseContext->setSerializationGroups($this->getSerializationGroups());

    return new ResourcesListResponse($pagination, $responseContext);
  }

  private function createCriteriaFrom(Request $request): Criteria {
    return Criteria::fromQueryParameters(
        $request->query->get('term', ''),
        array_merge($this->createDefaultCriteria($request), $this->createAdditionalCriteria($request)
        ));
  }

  private function createDefaultCriteria(Request $request): array {
    $currentTenant = $this->cachedTenantContext->getTenant();

    return [
        'page' => $request->query->get('page'),
        'sort' => $request->query->get('sorting'),
        'limit' => $request->query->get('limit', 10),
        'tenantCode' => $currentTenant->getCode(),
    ];
  }

  protected function getSerializationGroups(): array {
    return [
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
    ];
  }

  protected function createAdditionalCriteria(Request $request): array {
    return [
        'routes' => array_filter((array)$request->query->get('route', [])),
        'statuses' => array_filter((array)$request->query->get('status', [])),
        'authors' => array_filter((array)$request->query->get('author', [])),
        'publishedBefore' => $request->query->has('published_before') ? new \DateTime($request->query->get('published_before')) : null,
        'publishedAfter' => $request->query->has('published_after') ? new \DateTime($request->query->get('published_after')) : null,
        'publishedAt' => $request->query->get('published_at'),
        'sources' => array_filter((array)$request->query->get('source', [])),
        'metadata' => array_filter((array)$request->query->get('metadata', [])),
        'keywords' => array_filter((array)$request->query->get('keywords', [])),
    ];
  }
}
