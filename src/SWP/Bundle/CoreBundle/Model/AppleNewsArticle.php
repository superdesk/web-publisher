<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

class AppleNewsArticle implements AppleNewsArticleInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $articleId;

    /** @var string */
    protected $revisionId;

    /** @var string */
    protected $shareUrl;

    /** @var ArticleInterface */
    protected $article;

    public function getId()
    {
        return $this->id;
    }

    public function getArticleId(): string
    {
        return $this->articleId;
    }

    public function setArticleId(string $articleId): void
    {
        $this->articleId = $articleId;
    }

    public function getRevisionId(): string
    {
        return str_replace('\/', '/', $this->revisionId);
    }

    public function setRevisionId(string $revisionId): void
    {
        $this->revisionId = $revisionId;
    }

    public function getShareUrl(): string
    {
        return $this->shareUrl;
    }

    public function setShareUrl(string $shareUrl): void
    {
        $this->shareUrl = $shareUrl;
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
