<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Service;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSourceInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSourceReferenceInterface;

interface ArticleSourceServiceInterface
{
    /**
     * @param ArticleInterface       $article
     * @param ArticleSourceInterface $source
     *
     * @return ArticleSourceReferenceInterface
     */
    public function getArticleSourceReference(ArticleInterface $article, ArticleSourceInterface $source);
}
