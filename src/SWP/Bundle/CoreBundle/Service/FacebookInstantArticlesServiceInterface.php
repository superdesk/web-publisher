<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Service;

use Facebook\InstantArticles\Client\InstantArticleStatus;
use Facebook\InstantArticles\Elements\InstantArticle;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeedInterface;

interface FacebookInstantArticlesServiceInterface
{
    /**
     * @param FacebookInstantArticlesFeedInterface $feed
     * @param InstantArticle                       $instantArticle
     * @param ArticleInterface                     $article
     */
    public function pushInstantArticle(
        FacebookInstantArticlesFeedInterface $feed,
        InstantArticle $instantArticle,
        ArticleInterface $article
    );

    /**
     * @param string $submissionId
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function updateSubmissionStatus(string $submissionId);

    /**
     * @param FacebookInstantArticlesFeedInterface $feed
     * @param ArticleInterface                     $article
     *
     * @return null|InstantArticleStatus
     */
    public function removeInstantArticle(FacebookInstantArticlesFeedInterface $feed, ArticleInterface $article);
}
