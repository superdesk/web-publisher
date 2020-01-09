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

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Service\ArticleServiceInterface;

final class ArticlePublishListener
{
    private $articleService;

    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

    public function publish(ArticleEvent $event): void
    {
        $article = $event->getArticle();

        if ($article->isPublished()) {
            return;
        }

        $this->articleService->publish($article);
    }

    public function unpublish(ArticleEvent $event): void
    {
        $article = $event->getArticle();

        if ($article->isPublished()) {
            $this->articleService->unpublish($article, ArticleInterface::STATUS_UNPUBLISHED);
        }
    }

    public function cancel(ArticleEvent $event): void
    {
        $this->articleService->unpublish($event->getArticle(), ArticleInterface::STATUS_CANCELED);
    }
}
