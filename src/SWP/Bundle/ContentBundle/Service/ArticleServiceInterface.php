<?php

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

use SWP\Bundle\ContentBundle\Model\ArticleInterface;

interface ArticleServiceInterface
{
    public function publish(ArticleInterface $article): ArticleInterface;

    public function unpublish(ArticleInterface $article, string $newArticleStatus): ArticleInterface;

    public function reactOnStatusChange(string $originalArticleStatus, ArticleInterface $article): void;
}
