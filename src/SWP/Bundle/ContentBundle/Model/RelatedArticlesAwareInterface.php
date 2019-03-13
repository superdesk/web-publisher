<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\Collection;

interface RelatedArticlesAwareInterface
{
    public function addRelatedArticle(RelatedArticleInterface $relatedArticle): void;

    public function removeRelatedArticle(RelatedArticleInterface $relatedArticle): void;

    public function hasRelatedArticle(RelatedArticleInterface $relatedArticle): bool;

    public function getRelatedArticles(): Collection;
}
