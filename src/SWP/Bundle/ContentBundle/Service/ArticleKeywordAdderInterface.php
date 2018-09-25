<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Service;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;

interface ArticleKeywordAdderInterface
{
    /**
     * @param ArticleInterface $article
     * @param string           $name
     */
    public function add(ArticleInterface $article, string $name);
}
