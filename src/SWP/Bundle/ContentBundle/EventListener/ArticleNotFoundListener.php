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

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Component\Common\Exception\ArticleNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ArticleNotFoundListener
{
    private $router;

    private $redirectNotFoundArticles;

    public function __construct(RouterInterface $router, bool $redirectNotFoundArticles)
    {
        $this->router = $router;
        $this->redirectNotFoundArticles = $redirectNotFoundArticles;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();
        if (!$this->redirectNotFoundArticles || !$exception instanceof ArticleNotFoundException) {
            return;
        }

        $route = 'homepage';
        if ($request->attributes->has('routeMeta')) {
            $route = $request->attributes->get('routeMeta');
        }

        $event->setResponse(new RedirectResponse(
            $this->router->generate($route, [], UrlGeneratorInterface::ABSOLUTE_URL),
            Response::HTTP_MOVED_PERMANENTLY
        ));
    }
}
