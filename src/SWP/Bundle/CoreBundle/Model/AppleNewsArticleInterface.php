<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Component\Storage\Model\PersistableInterface;

interface AppleNewsArticleInterface extends PersistableInterface
{
    public function getId();

    public function getArticleId(): string;

    public function setArticleId(string $articleId): void;

    public function getRevisionId(): string;

    public function setRevisionId(string $revisionId): void;

    public function getShareUrl(): string;

    public function setShareUrl(string $shareUrl): void;

    public function getArticle(): ArticleInterface;

    public function setArticle(ArticleInterface $article): void;
}
