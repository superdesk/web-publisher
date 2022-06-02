<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Controller;

use SWP\Bundle\ContentBundle\Doctrine\RelatedArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Exception\NotFoundHttpException;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class RelatedArticleController extends AbstractController {
  private RelatedArticleRepositoryInterface $relatedArticleRepository;
  private ArticleProviderInterface $articleProvider;

  /**
   * @param RelatedArticleRepositoryInterface $relatedArticleRepository
   * @param ArticleProviderInterface $articleProvider
   */
  public function __construct(RelatedArticleRepositoryInterface $relatedArticleRepository,
                              ArticleProviderInterface          $articleProvider) {
    $this->relatedArticleRepository = $relatedArticleRepository;
    $this->articleProvider = $articleProvider;
  }


  public function listAction(Request $request, string $id) {
    $article = $this->findOr404($id);

    $repository = $this->relatedArticleRepository;

    $items = $repository->getPaginatedByCriteria(
        new Criteria([
            'article' => $article,
        ]),
        $request->query->all('sorting'),
        new PaginationData($request)
    );

    return new ResourcesListResponse($items);
  }

  private function findOr404(string $id): ArticleInterface {
    $article = $this->articleProvider->getOneById($id);

    if (null === $article) {
      throw new NotFoundHttpException(sprintf('Article "%s" was not found.', $id));
    }

    return $article;
  }
}
