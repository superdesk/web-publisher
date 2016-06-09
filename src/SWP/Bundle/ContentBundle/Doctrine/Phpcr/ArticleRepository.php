<?php

namespace SWP\Bundle\ContentBundle\Doctrine\Phpcr;

use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;

class ArticleRepository extends DocumentRepository implements ArticleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->findOneBy(['slug' => $slug]);
    }
}
