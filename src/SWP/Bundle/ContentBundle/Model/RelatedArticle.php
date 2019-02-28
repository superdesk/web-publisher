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

use SWP\Component\Common\Model\TimestampableTrait;

class RelatedArticle implements RelatedArticleInterface
{
    use TimestampableTrait;

    protected $id;

    /**
     * @var ArticleInterface
     */
    protected $relatesTo;

    /**
     * @var ArticleInterface
     */
    protected $article;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRelatesTo(): ?ArticleInterface
    {
        return $this->relatesTo;
    }

    public function setRelatesTo(?ArticleInterface $relatesTo): void
    {
        $this->relatesTo = $relatesTo;
    }

    public function getArticle(): ArticleInterface
    {
        return $this->article;
    }

    public function setArticle(ArticleInterface $article): void
    {
        $this->article = $article;
    }
}
