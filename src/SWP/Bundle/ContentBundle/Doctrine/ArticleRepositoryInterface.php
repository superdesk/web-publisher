<?php

namespace SWP\Bundle\ContentBundle\Doctrine;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;

interface ArticleRepositoryInterface
{
    /**
     * Finds one article by slug.
     * 
     * @param string $slug
     *
     * @return ArticleInterface
     */
    public function findOneBySlug($slug);
}
