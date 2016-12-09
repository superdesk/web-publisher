<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Factory;

use SWP\Bundle\ContentBundle\Hydrator\ArticleHydratorInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Component\Bridge\Model\PackageInterface;

abstract class AbstractArticleFactory implements ArticleFactoryInterface
{
    protected $articleHydrator;

    /**
     * AbstractArticleFactory constructor.
     *
     * @param ArticleHydratorInterface $articleHydrator
     */
    public function __construct(ArticleHydratorInterface $articleHydrator)
    {
        $this->articleHydrator = $articleHydrator;
    }

    /**
     * @param PackageInterface $package
     *
     * @return ArticleInterface
     */
    protected function hydrateArticle(PackageInterface $package)
    {
        /** @var ArticleInterface $article */
        $article = $this->create();

        return $this->articleHydrator->hydrate($article, $package);
    }
}
