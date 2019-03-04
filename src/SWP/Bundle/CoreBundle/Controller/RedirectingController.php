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

use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectingController extends Controller
{
    public function removeTrailingSlashAction(Request $request): RedirectResponse
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        return $this->redirect($url, 301);
    }

    public function redirectBasedOnExtraDataAction(string $key, string $value): RedirectResponse
    {
        $articleRepository = $this->container->get('swp.repository.article');
        $existingArticle = $articleRepository->getArticleByPackageExtraData($key, $value)->getQuery()->getOneOrNullResult();

        if (null === $existingArticle || null === $existingArticle->getRoute()) {
            throw $this->createNotFoundException('Article with provided data was not found.');
        }
        $url = $this->generateArticleUrl($existingArticle);

        return $this->redirect($url, 301);
    }

    public function redirectBasedOnSlugAction(string $slug): RedirectResponse
    {
        $articleRepository = $this->container->get('swp.repository.article');
        $article = $articleRepository->findOneBySlug($slug);

        if (null === $article || null === $article->getRoute()) {
            throw $this->createNotFoundException('Article not found.');
        }
        $url = $this->generateArticleUrl($article);

        return $this->redirect($url, 301);
    }

    private function generateArticleUrl(ArticleInterface $article): string
    {
        $urlGenerator = $this->container->get('cmf_routing.generator');

        return $urlGenerator->generate($article->getRoute(), ['slug' => $article->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
