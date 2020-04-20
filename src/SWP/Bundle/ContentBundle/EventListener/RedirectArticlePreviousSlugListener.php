<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Component\Common\Exception\ArticleNotFoundException;
use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

final class RedirectArticlePreviousSlugListener
{
    private $router;

    private $articleSlugRepository;

    public function __construct(RouterInterface $router, RepositoryInterface $articleSlugRepository)
    {
        $this->router = $router;
        $this->articleSlugRepository = $articleSlugRepository;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getException();
        $request = $event->getRequest();

        if (!$exception instanceof NotFoundHttpException && !$exception instanceof ArticleNotFoundException) {
            return;
        }

        $articleSlug = $this->articleSlugRepository->findOneBy(['slug' => $request->getRequestUri()]);

        if (null !== $articleSlug) {
            $article = $articleSlug->getArticle();

            $event->setResponse(new RedirectResponse(
                $this->router->generate(
                    $article->getRoute()->getName(),
                    [
                        'slug' => $article->getSlug(),
                    ]
                ),
                Response::HTTP_MOVED_PERMANENTLY
            ));
        }
    }
}
