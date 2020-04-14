<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Model;

class ArticleSlug implements ArticleSlugInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $slug;

    /** @var ArticleInterface|null */
    protected $article;

    public function getId()
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
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
