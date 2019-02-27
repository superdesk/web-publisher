<?php

declare(strict_types=1);

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
