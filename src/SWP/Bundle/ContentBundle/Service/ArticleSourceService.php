<?php

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

namespace SWP\Bundle\ContentBundle\Service;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSourceInterface;
use SWP\Bundle\ContentBundle\Model\ArticleSourceReferenceInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

class ArticleSourceService implements ArticleSourceServiceInterface
{
    /**
     * @var FactoryInterface
     */
    private $articleSourceReferenceFactory;

    /**
     * @var RepositoryInterface
     */
    private $articleSourceReferenceRepository;

    /**
     * ArticleSourceService constructor.
     *
     * @param FactoryInterface    $articleSourceReferenceFactory
     * @param RepositoryInterface $articleSourceReferenceRepository
     */
    public function __construct(FactoryInterface $articleSourceReferenceFactory, RepositoryInterface $articleSourceReferenceRepository)
    {
        $this->articleSourceReferenceFactory = $articleSourceReferenceFactory;
        $this->articleSourceReferenceRepository = $articleSourceReferenceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getArticleSourceReference(ArticleInterface $article, ArticleSourceInterface $source)
    {
        $articleSourceReference = $this->articleSourceReferenceRepository->findBy(['article' => $article, 'articleSource' => $source]);
        if (count($articleSourceReference) > 0) {
            return reset($articleSourceReference);
        }

        /** @var ArticleSourceReferenceInterface $articleSourceReference */
        $articleSourceReference = $this->articleSourceReferenceFactory->create();
        $articleSourceReference->setArticle($article);
        $articleSourceReference->setArticleSource($source);

        return $articleSourceReference;
    }
}
