<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Repository;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeedInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

interface FacebookInstantArticlesArticleRepositoryInterface extends RepositoryInterface
{
    /**
     * @param FacebookInstantArticlesFeedInterface $feed
     * @param ArticleInterface                     $article
     *
     * @return mixed
     */
    public function findInFeed(FacebookInstantArticlesFeedInterface $feed, ArticleInterface $article);

    /**
     * @param ArticleInterface $article
     *
     * @return mixed
     */
    public function findByArticle(ArticleInterface $article);

    /**
     * @param string $submissionId
     *
     * @return mixed
     */
    public function findSubmission(string $submissionId);
}
