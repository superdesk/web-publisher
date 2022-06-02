<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use SWP\Bundle\CoreBundle\Repository\FacebookInstantArticlesArticleRepository;
use SWP\Bundle\CoreBundle\Service\FacebookInstantArticlesService;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FbiaArticleController extends Controller {

  private FacebookInstantArticlesArticleRepository $facebookInstantArticlesArticleRepository;
  private FacebookInstantArticlesService $facebookInstantArticlesService;
  private EventDispatcherInterface $eventDispatcher;

  /**
   * @param FacebookInstantArticlesArticleRepository $facebookInstantArticlesArticleRepository
   * @param FacebookInstantArticlesService $facebookInstantArticlesService
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(FacebookInstantArticlesArticleRepository $facebookInstantArticlesArticleRepository,
                              FacebookInstantArticlesService           $facebookInstantArticlesService,
                              EventDispatcherInterface                 $eventDispatcher) {
    $this->facebookInstantArticlesArticleRepository = $facebookInstantArticlesArticleRepository;
    $this->facebookInstantArticlesService = $facebookInstantArticlesService;
    $this->eventDispatcher = $eventDispatcher;
  }


  /**
   * @Route("/api/{version}/facebook/instantarticles/articles/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_list_facebook_instant_articles_articles")
   */
  public function listAction(Request $request): ResourcesListResponseInterface {
    $repository = $this->facebookInstantArticlesArticleRepository;
    $sort = $request->query->all('sorting');
    if (empty($sort)) {
      $sort = ['createdAt' => 'desc'];
    }
    $items = $repository->getPaginatedByCriteria(
        $this->eventDispatcher,
        new Criteria(),
        $sort,
        new PaginationData($request)
    );

    return new ResourcesListResponse($items);
  }

  /**
   * @Route("/api/{version}/facebook/instantarticles/articles/{submissionId}", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_facebook_instant_articles_articles_update")
   */
  public function updateSubmissionAction(string $submissionId): SingleResourceResponseInterface {
    $instantArticlesService = $this->facebookInstantArticlesService;
    $instantArticle = $instantArticlesService->updateSubmissionStatus($submissionId);

    return new SingleResourceResponse($instantArticle);
  }
}
