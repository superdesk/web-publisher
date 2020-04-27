<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

class ArticlePreviousRelativeUrl implements ArticlePreviousRelativeUrlInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $relativeUrl;

    /** @var ArticleInterface|null */
    protected $article;

    public function getId()
    {
        return $this->id;
    }

    public function getRelativeUrl(): string
    {
        return $this->relativeUrl;
    }

    public function setRelativeUrl(string $relativeUrl): void
    {
        $this->relativeUrl = $relativeUrl;
    }

    public function getArticle(): ?ArticleInterface
    {
        return $this->article;
    }

    public function setArticle(?ArticleInterface $article): void
    {
        $this->article = $article;
    }
}
