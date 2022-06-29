<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use SWP\Bundle\ContentBundle\Doctrine\SlideshowRepositoryInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations\Route;

class SlideshowController extends Controller {
  private ArticleRepositoryInterface $articleRepository;
  private SlideshowRepositoryInterface $slideshowRepository;
  private EventDispatcherInterface $eventDispatcher;

  /**
   * @param ArticleRepositoryInterface $articleRepository
   * @param SlideshowRepositoryInterface $slideshowRepository
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(ArticleRepositoryInterface   $articleRepository,
                              SlideshowRepositoryInterface $slideshowRepository,
                              EventDispatcherInterface     $eventDispatcher) {
    $this->articleRepository = $articleRepository;
    $this->slideshowRepository = $slideshowRepository;
    $this->eventDispatcher = $eventDispatcher;
  }


  /**
   * @Route("/api/{version}/content/slideshows/{articleId}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_slideshows_list")
   */
  public function listAction(Request $request, string $articleId): ResourcesListResponseInterface {
    $repository = $this->slideshowRepository;

    $article = $this->findArticleOr404($articleId);

    $slideshows = $repository->getPaginatedByCriteria($this->eventDispatcher, new Criteria([
        'article' => $article,
    ]), $request->query->all('sorting'), new PaginationData($request));

    return new ResourcesListResponse($slideshows);
  }

  /**
   * @Route("/api/{version}/content/slideshows/{articleId}/{id}", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_get_slideshow", requirements={"id"="\d+"})
   */
  public function getAction($id, string $articleId): SingleResourceResponseInterface {
    $article = $this->findArticleOr404($articleId);

    if (null === $list = $this->slideshowRepository->findOneBy([
            'id' => $id,
            'article' => $article,
        ])) {
      throw new NotFoundHttpException(sprintf('Slideshow with id "%s" was not found.', $id));
    }

    return new SingleResourceResponse($list);
  }

  private function findArticleOr404($id) {
    if (null === $article = $this->articleRepository->findOneById($id)) {
      throw new NotFoundHttpException(sprintf('Article with id "%s" was not found.', $id));
    }

    return $article;
  }
}
