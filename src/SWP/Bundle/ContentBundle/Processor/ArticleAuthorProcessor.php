<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Processor;

use SWP\Bundle\ContentBundle\Model\ArticleAuthor;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;

class ArticleAuthorProcessor
{
    public static function processArticleAuthors(ArticleInterface $article): void
    {
        foreach ($article->getAuthors() as $articleAuthor) {
            self::setSlugInArticleAuthor($articleAuthor);
        }
    }

    public static function setSlugInArticleAuthor(ArticleAuthor $articleAuthor): void
    {
        if (null === $articleAuthor->getSlug()) {
            $articleAuthor->setSlug($articleAuthor->getName());
        }
    }
}
