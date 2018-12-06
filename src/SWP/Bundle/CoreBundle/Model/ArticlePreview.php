<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

final class ArticlePreview
{
    /**
     * @var ArticleInterface
     */
    private $article;

    /**
     * @var string|null
     */
    private $previewUrl;

    public function getArticle(): ArticleInterface
    {
        return $this->article;
    }

    public function setArticle(ArticleInterface $article): void
    {
        $this->article = $article;
    }

    public function getPreviewUrl(): ?string
    {
        return $this->previewUrl;
    }

    public function setPreviewUrl(?string $previewUrl): void
    {
        $this->previewUrl = $previewUrl;
    }
}
