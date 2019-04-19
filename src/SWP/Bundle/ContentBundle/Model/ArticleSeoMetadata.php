<?php

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Seo\Model\SeoMetadata;

class ArticleSeoMetadata extends SeoMetadata implements ArticleSeoMetadataInterface
{
    /**
     * @var ArticleInterface
     */
    protected $article;

    public function getArticle(): ArticleInterface
    {
        return $this->article;
    }

    public function setArticle(ArticleInterface $article): void
    {
        $this->article = $article;
    }
}
