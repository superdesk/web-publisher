<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Service;

use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ArticleService implements ArticleServiceInterface
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function publish(ArticleInterface $article): ArticleInterface
    {
        $this->checkIfCanBePublishedOrUnpublished($article, 'Article cannot be published');

        $article->setPublishable(true);
        $article->setStatus(ArticleInterface::STATUS_PUBLISHED);
        if (null === $article->getPublishedAt()) {
            $article->setPublishedAt(new \DateTime());
        }

        $this->dispatchArticleEvent(ArticleEvents::POST_PUBLISH, $article);

        return $article;
    }

    public function unpublish(ArticleInterface $article, string $status): ArticleInterface
    {
        $this->checkIfCanBePublishedOrUnpublished($article, 'Article cannot be unpublished');

        $article->setPublishable(false);
        $article->setStatus($status);

        $this->dispatchArticleEvent(ArticleEvents::POST_UNPUBLISH, $article);

        return $article;
    }

    public function reactOnStatusChange(string $originalArticleStatus, ArticleInterface $article): void
    {
        $newArticleStatus = $article->getStatus();
        if ($originalArticleStatus === $newArticleStatus) {
            return;
        }

        if (ArticleInterface::STATUS_PUBLISHED === $newArticleStatus) {
            $this->publish($article);
        } else {
            $this->unpublish($article, $newArticleStatus);
        }
    }

    private function checkIfCanBePublishedOrUnpublished($article, $exceptionMessage)
    {
        $currentTime = new \DateTime();
        if ((null !== $article->getPublishStartDate() && $currentTime < $article->getPublishStartDate()) ||
            (null !== $article->getPublishEndDate() && $currentTime > $article->getPublishEndDate())
        ) {
            throw new \Exception($exceptionMessage);
        }
    }

    private function dispatchArticleEvent($eventName, ArticleInterface $article)
    {
        $this->eventDispatcher->dispatch(new ArticleEvent($article, null, $eventName), $eventName);
    }
}
