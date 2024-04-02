<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class RedirectingController extends AbstractController {

  private RouterInterface $router;
  private ArticleRepositoryInterface $articleRepository;

  /**
   * @param RouterInterface $router
   * @param ArticleRepositoryInterface $articleRepository
   */
  public function __construct(RouterInterface $router, ArticleRepositoryInterface $articleRepository) {
    $this->router = $router;
    $this->articleRepository = $articleRepository;
  }


  public function redirectBasedOnExtraDataAction(Request $request, string $key, string $value): RedirectResponse {
    try {
      $existingArticle = $this->articleRepository->getArticleByExtraData($key, $value)->getQuery()->getOneOrNullResult();
      if (null === $existingArticle) {
        $existingArticle = $this->articleRepository->getArticleByPackageExtraData($key, $value)->getQuery()->getOneOrNullResult();
      }
    } catch (NonUniqueResultException $e) {
      $existingArticle = null;
    }

    if (null === $existingArticle || null === $existingArticle->getRoute()) {
      throw $this->createNotFoundException('Article with provided data was not found.');
    }

    return $this->redirect($this->generateArticleUrl($request, $existingArticle), 301);
  }

  public function redirectBasedOnSlugAction(Request $request, string $slug): RedirectResponse {
    $existingArticle = $this->articleRepository->findOneBySlug($slug);
    if (null === $existingArticle || null === $existingArticle->getRoute()) {
      throw $this->createNotFoundException('Article not found.');
    }

    return $this->redirect($this->generateArticleUrl($request, $existingArticle), 301);
  }

  private function generateArticleUrl(Request $request, ArticleInterface $article): string {
    $parameters = ['slug' => $article->getSlug()];
    if ($request->query->has('amp')) {
      $parameters['amp'] = 1;
    }

    $relativeUrl = $this->router->generate($article->getRoute(), $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
    $customDomain = 'https://www.brasil247.com';
    $absoluteUrl = rtrim($customDomain, '/') . '/' . ltrim($relativeUrl, '/');
    return $absoluteUrl;
  }
}
