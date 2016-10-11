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

namespace SWP\Bundle\ContentBundle\Provider\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use SWP\Bundle\ContentBundle\Criteria\Criteria;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Pagination\PaginationData;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ArticleProvider to provide articles from ORM.
 */
class ArticleProvider implements ArticleProviderInterface
{
    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * ArticleProvider constructor.
     *
     * @param ArticleRepositoryInterface $articleRepository
     * @param PaginatorInterface         $paginator
     */
    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        PaginatorInterface $paginator
    ) {
        $this->articleRepository = $articleRepository;
        $this->paginator = $paginator;
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
        return $this->articleRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getTenantArticlesQuery(string $tenantContentIdentifier, array $order)
    {
        return $this->articleRepository->getQueryForTenantArticles($tenantContentIdentifier, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteArticlesQuery(string $routeIdentifier, array $order)
    {
        return $this->articleRepository->getQueryForRouteArticles($routeIdentifier, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function getOneByCriteria(Criteria $criteria): ArticleInterface
    {
        $criteria->set('maxResults', 1);
        $article = $this->articleRepository->getByCriteria($criteria)->getOneOrNullResult();
        if (null === $article) {
            throw new NotFoundHttpException('Article was not found');
        }

        return $article;
    }

    public function getManyByCriteria(Criteria $criteria): Collection
    {
        $results = $this->articleRepository->getByCriteria($criteria)->getResult();

        return new ArrayCollection($results);
    }

    public function getPaginatedByCriteria(Criteria $criteria, PaginationData $paginationData): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->articleRepository->getByCriteria($criteria),
            $paginationData->getFirstResult(),
            $paginationData->getLimit()
        );
    }
}
