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
    /**
     * Make article publishable.
     *
     * @param ArticleInterface $article
     *
     * @return ArticleInterface
     */
    public function publish(ArticleInterface $article);

    /**
     * @param ArticleInterface $article
     * @param string           $newArticleStatus
     */
    public function unpublish(ArticleInterface $article, string $newArticleStatus);

    /**
     * @param string           $originalArticleStatus
     * @param ArticleInterface $article
     */
    public function reactOnStatusChange(string $originalArticleStatus, ArticleInterface $article);
}
