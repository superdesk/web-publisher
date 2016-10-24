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

namespace SWP\Bundle\ContentBundle\Provider\ODM\PHPCR;

use Jackalope\Query\SqlQuery;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
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
     * @var string
     */
    private $basePath;

    /**
     * ArticleProvider constructor.
     *
     * @param ArticleRepositoryInterface      $articleRepository
     * @param TenantAwarePathBuilderInterface $pathBuilder
     * @param string                          $basePath
     */
    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        TenantAwarePathBuilderInterface $pathBuilder,
        string $basePath
    ) {
        $this->articleRepository = $articleRepository;
        $this->pathBuilder = $pathBuilder;
        $this->basePath = $basePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseNode()
    {
        return $this->articleRepository->findBaseNode($this->pathBuilder->build($this->basePath));
    }

    /**
     * {@inheritdoc}
     */
    public function getOneById($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            return $this->articleRepository->findOneBySlug($id);
        }

        return $this->articleRepository->findOneBy(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent($id)
    {
        return $this->articleRepository->find($this->pathBuilder->build($id));
    }

    /**
     * {@inheritdoc}
     */
    public function getTenantArticlesQuery(string $tenantContentIdentifier, array $order) : SqlQuery
    {
        return $this->articleRepository->getQueryForTenantArticles($tenantContentIdentifier, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteArticlesQuery(string $routeIdentifier, array $order) : SqlQuery
    {
        return $this->articleRepository->getQueryForRouteArticles($routeIdentifier, $order);
    }
}
