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

namespace SWP\Bundle\ContentBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;

class AttachArticleToContentRouteListener
{
    /** @var RouteRepositoryInterface */
    private $routeRepository;

    public function __construct(RouteRepositoryInterface $routeRepository)
    {
        $this->routeRepository = $routeRepository;
    }

    public function onArticlePublish(ArticleEvent $articleEvent): void
    {
        /** @var ArticleInterface $article */
        $article = $articleEvent->getArticle();
        $route = $article->getRoute();
        $alreadyAttachedRoute = $this->routeRepository->findOneBy(['content' => $article]);

        if (null !== $route && !$alreadyAttachedRoute && RouteInterface::TYPE_CONTENT === $route->getType()) {
            $route->setContent($article);
            $this->routeRepository->flush();
        }
    }

    public function onArticleUnpublish(ArticleEvent $articleEvent): void
    {
        $route = $articleEvent->getArticle()->getRoute();

        if ($route && RouteInterface::TYPE_CONTENT === $route->getType()) {
            $route->setContent(null);
            $this->routeRepository->flush();
        }
    }
}
