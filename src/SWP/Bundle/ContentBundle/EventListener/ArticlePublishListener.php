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
    /**
     * @var ArticleServiceInterface
     */
    private $articleService;

    /**
     * ArticlePublishListener constructor.
     *
     * @param ArticleServiceInterface $articleService
     */
    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * @param ArticleEvent $event
     */
    public function publish(ArticleEvent $event)
    {
        $article = $event->getArticle();

        if (isset($article->getExtra()['update_date'])) {
            $article->cancelTimestampable();
        }

        if ($article->isPublished()) {
            return;
        }

        $this->articleService->publish($article);
    }

    /**
     * @param ArticleEvent $event
     */
    public function unpublish(ArticleEvent $event)
    {
        $article = $event->getArticle();

        if ($article->isPublished()) {
            $this->articleService->unpublish($article, ArticleInterface::STATUS_UNPUBLISHED);
        }
    }

    /**
     * @param ArticleEvent $event
     */
    public function cancel(ArticleEvent $event)
    {
        $this->articleService->unpublish($event->getArticle(), ArticleInterface::STATUS_CANCELED);
    }
}
