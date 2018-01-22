<?php

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

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\Collection;

interface ArticlesAwareInterface
{
    /**
     * @return Collection
     */
    public function getArticles(): Collection;

    /**
     * @param Collection $articles
     */
    public function setArticles(Collection $articles): void;

    /**
     * @param ArticleInterface $article
     */
    public function addArticle(ArticleInterface $article): void;
}
