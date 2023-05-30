<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\CoreBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Repository\ArticleRepositoryInterface;
use SWP\Component\Common\Generator\GeneratorInterface;

class DuplicatedSlugListener
{
    /**
     * @var ArticleRepositoryInterface
     */
    protected $articleRepository;

    /**
     * @var GeneratorInterface
     */
    protected $stringGenerator;

    protected string $slugRegex;

    public function __construct(ArticleRepositoryInterface $articleRepository, GeneratorInterface $stringGenerator, string $slugRegexp = '')
    {
        $this->articleRepository = $articleRepository;
        $this->stringGenerator = $stringGenerator;
        $this->slugRegex = $slugRegexp;
    }

    public function onArticleCreate(ArticleEvent $event): void
    {
        /** @var ArticleInterface $article */
        $article = $event->getArticle();

        $existingArticle = $this->articleRepository
            ->getArticleBySlugForPackage($article->getSlug(), $article->getPackage())
            ->getQuery()
            ->getOneOrNullResult();

        $regexpMatched = false;
        if (!empty($this->slugRegex) && 1 === preg_match($this->slugRegex, $article->getSlug())) {
            $regexpMatched = true;
        }

        if (null === $existingArticle && !$regexpMatched) {
            return;
        }

        $article->setSlug($article->getSlug().'-'.$this->stringGenerator->generate(8));
    }
}
