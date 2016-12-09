<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Hydrator;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Bridge\Model\PackageInterface;

interface ArticleHydratorInterface
{
    /**
     * @param ArticleInterface $article
     * @param PackageInterface $package
     *
     * @return ArticleInterface
     */
    public function hydrate(ArticleInterface $article, PackageInterface $package): ArticleInterface;
}
