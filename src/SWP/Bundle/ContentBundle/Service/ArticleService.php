<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Service;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Service\ArticleServiceInterface;

class ArticleService implements ArticleServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function publish(ArticleInterface $article)
    {
        $this->checkIfCanBePublishedOrUnpublished($article, "Article cannot be published");

        $article->setPublishable(true);
        $article->setStatus(ArticleInterface::STATUS_PUBLISHED);
        $article->setPublishedAt(new \DateTime());

        return $article;
    }

    /**
     * {@inheritdoc}
     */
    public function unpublish(ArticleInterface $article, $status)
    {
        $this->checkIfCanBePublishedOrUnpublished($article, "Article cannot be unpublished");

        $article->setPublishable(false);
        $article->setStatus($status);

        return $article;
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
}
