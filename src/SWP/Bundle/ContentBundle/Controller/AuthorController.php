<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2020 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Controller;

use SWP\Bundle\ContentBundle\Doctrine\ArticleAuthorRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations\Route;

class AuthorController extends AbstractController {

  private ArticleAuthorRepositoryInterface $articleAuthorRepository; // swp.repository.author

  /**
   * @param ArticleAuthorRepositoryInterface $articleAuthorRepository
   */
  public function __construct(ArticleAuthorRepositoryInterface $articleAuthorRepository) {
    $this->articleAuthorRepository = $articleAuthorRepository;
  }

  /**
   * @Route("/api/{version}/authors/{id}", methods={"DELETE"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_core_delete_author", requirements={"id"="\d+"})
   */
  public function deleteAction(int $id): SingleResourceResponseInterface {
    $authorRepository = $this->articleAuthorRepository;
    $author = $this->findOr404($id);

    $authorRepository->remove($author);

    return new SingleResourceResponse(null, new ResponseContext(204));
  }

  private function findOr404(int $id): ArticleAuthorInterface {
    if (null === $author = $this->articleAuthorRepository->findOneById($id)) {
      throw new NotFoundHttpException('Author was not found.');
    }

    return $author;
  }
}
