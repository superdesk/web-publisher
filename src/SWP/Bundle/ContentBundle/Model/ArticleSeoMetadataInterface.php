<?php

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Seo\Model\SeoMetadataInterface;

interface ArticleSeoMetadataInterface extends SeoMetadataInterface
{
    public function getArticle(): ArticleInterface;

    public function setArticle(ArticleInterface $article): void;
}
