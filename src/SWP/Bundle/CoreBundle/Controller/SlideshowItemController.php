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

use SWP\Bundle\ContentBundle\Doctrine\SlideshowItemRepositoryInterface;
use SWP\Bundle\ContentBundle\Doctrine\SlideshowRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\SlideshowInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Route;

class SlideshowItemController extends Controller {
  private ArticleRepositoryInterface $articleRepository;
  private SlideshowRepositoryInterface $slideshowRepository;
  private SlideshowItemRepositoryInterface $slideshowItemRepository;
  private EventDispatcherInterface $eventDispatcher;

  /**
   * @param ArticleRepositoryInterface $articleRepository
   * @param SlideshowRepositoryInterface $slideshowRepository
   * @param SlideshowItemRepositoryInterface $slideshowItemRepository
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(ArticleRepositoryInterface       $articleRepository,
                              SlideshowRepositoryInterface     $slideshowRepository,
                              SlideshowItemRepositoryInterface $slideshowItemRepository,
                              EventDispatcherInterface         $eventDispatcher) {
    $this->articleRepository = $articleRepository;
    $this->slideshowRepository = $slideshowRepository;
    $this->slideshowItemRepository = $slideshowItemRepository;
    $this->eventDispatcher = $eventDispatcher;
  }


  /**
   * @Route("/api/{version}/content/slideshows/{articleId}/{id}/items/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_core_slideshow_items", requirements={"id"="\d+"})
   */
  public function listAction(Request $request, string $articleId, string $id) {
    $article = $this->findArticleOr404($articleId);
    $repository = $this->slideshowItemRepository;

    $items = $repository->getPaginatedByCriteria(
        $this->eventDispatcher,
        new Criteria([
            'article' => $article,
            'slideshow' => $this->findOr404($id),
        ]),
        $request->query->all('sorting'),
        new PaginationData($request)
    );

    return new ResourcesListResponse($items);
  }

  private function findOr404($id): ?SlideshowInterface {
    if (null === $slideshow = $this->slideshowRepository->findOneById($id)) {
      throw new NotFoundHttpException(sprintf('Slideshow with id "%s" was not found.', $id));
    }

    return $slideshow;
  }

  private function findArticleOr404($id) {
    if (null === $article = $this->articleRepository->findOneById($id)) {
      throw new NotFoundHttpException(sprintf('Article with id "%s" was not found.', $id));
    }

    return $article;
  }
}
