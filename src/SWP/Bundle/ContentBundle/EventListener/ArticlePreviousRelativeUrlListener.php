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

use SWP\Component\Storage\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

final class ArticlePreviousRelativeUrlListener
{
    private $router;

    private $articlePreviousUrlRepository;

    public function __construct(RouterInterface $router, RepositoryInterface $articlePreviousUrlRepository)
    {
        $this->router = $router;
        $this->articlePreviousUrlRepository = $articlePreviousUrlRepository;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (!$exception instanceof NotFoundHttpException) {
            return;
        }

        $articleRelativeUrl = $this->articlePreviousUrlRepository->findOneBy(['relativeUrl' => $request->getRequestUri()]);

        if (null !== $articleRelativeUrl) {
            $article = $articleRelativeUrl->getArticle();

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
