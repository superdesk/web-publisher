<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Provider;

use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;

/**
 * ArticleProvider to provide articles based on PHPCR paths.
 */
class ArticleProvider implements ArticleProviderInterface
{
    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var TenantAwarePathBuilderInterface
     */
    private $pathBuilder;

    /**
     * ArticleProvider constructor.
     *
     * @param ArticleRepositoryInterface      $articleRepository
     * @param TenantAwarePathBuilderInterface $pathBuilder
     */
    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        TenantAwarePathBuilderInterface $pathBuilder
    ) {
        $this->articleRepository = $articleRepository;
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneById($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            return $this->articleRepository->findOneBySlug($id);
        }

        return $this->articleRepository->findOneById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllArticles()
    {
        return $this->articleRepository->createQueryBuilder('o')->getQuery();
    }
}
