<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Analytics Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\AnalyticsBundle\Repository;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;

interface ArticleEventRepositoryInterface
{
    public function getCountForArticleInternalPageViews(ArticleInterface $article): int;

    public function getCountForArticleAllPageViews(ArticleInterface $article): int;

    public function getCountForArticleAllImpressions(ArticleInterface $article): int;
}
