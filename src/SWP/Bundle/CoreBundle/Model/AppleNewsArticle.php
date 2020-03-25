<?php

declare(strict_types=1);

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
        return $this->revisionId;
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
