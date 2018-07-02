<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;

class DuplicatedSlugListener
{
    /**
     * @var ArticleRepositoryInterface
     */
    protected $articleRepository;

    /**
     * @param ArticleRepositoryInterface $articleRepository
     */
    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param ArticleEvent $event
     */
    public function onArticleCreate(ArticleEvent $event)
    {
        /** @var ArticleInterface $article */
        $article = $event->getArticle();

        $existingArticle = $this->articleRepository
            ->getArticleBySlugForPackage($article->getSlug(), $article->getPackage())
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $existingArticle) {
            return;
        }

        $hash = substr(md5($article->getPackage()->getGuid()), 0, 8);
        $article->setSlug($article->getSlug().'-'.$hash);
    }
}
