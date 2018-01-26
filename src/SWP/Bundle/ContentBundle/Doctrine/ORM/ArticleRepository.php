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

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Elastica\Query;
use SWP\Bundle\ContentBundle\Model\ArticleSourceReference;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Pagination\PaginationData;

/**
 * Class ArticleRepository.
 */
class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug)
    {
        return $this->findOneBy([
            'slug' => $slug,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllArticles()
    {
        return $this->getQueryByCriteria(new Criteria(), [], 'a')->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getByCriteria(Criteria $criteria, array $sorting): QueryBuilder
    {
        $qb = $this->getQueryByCriteria($criteria, $sorting, 'a');
        $qb->andWhere('a.status = :status')
            ->setParameter('status', $criteria->get('status', ArticleInterface::STATUS_PUBLISHED))
            ->leftJoin('a.media', 'm')
            ->leftJoin('m.renditions', 'r')
            ->leftJoin('a.sources', 's')
            ->leftJoin('a.authors', 'au')
            ->addSelect('m', 's', 'r', 'au');

        $this->applyCustomFiltering($qb, $criteria);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function countByCriteria(Criteria $criteria, $status = ArticleInterface::STATUS_PUBLISHED): int
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)');

        if (null !== $status) {
            $queryBuilder
                ->where('a.status = :status')
                ->setParameter('status', $criteria->get('status', $status));
        }

        $this->applyCustomFiltering($queryBuilder, $criteria);
        $this->applyCriteria($queryBuilder, $criteria, 'a');

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getArticlesByCriteria(Criteria $criteria, array $sorting = []): QueryBuilder
    {
        $queryBuilder = $this->getArticlesByCriteriaIds($criteria);
        $this->applyCustomFiltering($queryBuilder, $criteria);
        $this->applyCriteria($queryBuilder, $criteria, 'a');
        $this->applySorting($queryBuilder, $sorting, 'a');
        $articlesQueryBuilder = clone $queryBuilder;
        $this->applyLimiting($queryBuilder, $criteria);
        $selectedArticles = $queryBuilder->getQuery()->getScalarResult();

        if (!is_array($selectedArticles)) {
            return [];
        }

        $ids = [];

        foreach ($selectedArticles as $partialArticle) {
            $ids[] = $partialArticle['a_id'];
        }
        $articlesQueryBuilder->select('a')
            ->leftJoin('a.media', 'm')
            ->leftJoin('m.renditions', 'r')
            ->leftJoin('a.sources', 's')
            ->leftJoin('a.authors', 'au')
            ->addSelect('m', 'r', 's', 'au')
            ->andWhere('a.id IN (:ids)')
            ->setParameter('ids', $ids);

        return $articlesQueryBuilder;
    }

    /**
     * @param Criteria $criteria
     *
     * @return QueryBuilder
     */
    public function getArticlesByCriteriaIds(Criteria $criteria): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('partial a.{id}')
            ->where('a.status = :status')
            ->setParameter('status', $criteria->get('status', ArticleInterface::STATUS_PUBLISHED));

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedByCriteria(Criteria $criteria, array $sorting = [], PaginationData $paginationData = null)
    {
        $queryBuilder = $this->getQueryByCriteria($criteria, $sorting, 'a');
        $this->applyCustomFiltering($queryBuilder, $criteria);

        if (null === $paginationData) {
            $paginationData = new PaginationData();
        }

        return $this->getPaginator($queryBuilder, $paginationData);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryForRouteArticles(string $identifier, array $order = [])
    {
        throw new \Exception('Not implemented');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Criteria     $criteria
     */
    private function applyCustomFiltering(QueryBuilder $queryBuilder, Criteria $criteria)
    {
        foreach (['metadata'] as $name) {
            if (!$criteria->has($name)) {
                continue;
            }

            if (!is_array($criteria->get($name))) {
                $criteria->remove($name);

                continue;
            }

            $orX = $queryBuilder->expr()->orX();
            foreach ($criteria->get($name) as $value) {
                $valueExpression = $queryBuilder->expr()->literal('%'.$value.'%');
                $orX->add($queryBuilder->expr()->like('a.metadata', $valueExpression));
            }

            $queryBuilder->andWhere($orX);
            $criteria->remove($name);
        }

        if ($criteria->has('keywords')) {
            $orX = $queryBuilder->expr()->orX();
            foreach ($criteria->get('keywords') as $value) {
                $valueExpression = $queryBuilder->expr()->literal('%'.$value.'%');
                $orX->add($queryBuilder->expr()->like('a.keywords', $valueExpression));
            }

            $queryBuilder->andWhere($orX);
            $criteria->remove('keywords');
        }

        if ($criteria->has('publishedBefore') && $criteria->get('publishedBefore') instanceof \DateTime) {
            $queryBuilder->andWhere('a.publishedAt < :before')
                ->setParameter('before', $criteria->get('publishedBefore'));
            $criteria->remove('publishedBefore');
        }

        if ($criteria->has('publishedAfter') && $criteria->get('publishedAfter') instanceof \DateTime) {
            $queryBuilder->andWhere('a.publishedAt > :after')
                ->setParameter('after', $criteria->get('publishedAfter'));
            $criteria->remove('publishedAfter');
        }

        if ($criteria->has('query') && strlen($query = trim($criteria->get('query'))) > 0) {
            $like = $queryBuilder->expr()->like('a.title', $queryBuilder->expr()->literal('%'.$query.'%'));

            $queryBuilder->andWhere($like);
            $criteria->remove('query');
        }

        if ($criteria->has('exclude_source') && !empty($criteria->get('exclude_source'))) {
            $articleSourcesQueryBuilder = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('excluded_article.id')
                ->from(ArticleSourceReference::class, 'excluded_asr')
                ->join('excluded_asr.article', 'excluded_article')
                ->join('excluded_asr.articleSource', 'excluded_articleSource');

            $orX = $queryBuilder->expr()->orX();
            foreach ((array) $criteria->get('exclude_source') as $value) {
                $orX->add($articleSourcesQueryBuilder->expr()->eq('excluded_articleSource.name', $articleSourcesQueryBuilder->expr()->literal($value)));
            }
            $articleSourcesQueryBuilder->andWhere($orX);
            $queryBuilder->andWhere($queryBuilder->expr()->notIn('a.id', $articleSourcesQueryBuilder->getQuery()->getDQL()));

            $criteria->remove('exclude_source');
        }

        if ($criteria->has('source') && !empty($criteria->get('source'))) {
            $articleSourcesQueryBuilder = $this->getEntityManager()
                ->createQueryBuilder()
                ->select('article.id')
                ->from(ArticleSourceReference::class, 'asr')
                ->join('asr.article', 'article')
                ->join('asr.articleSource', 'articleSource');
            $orX = $queryBuilder->expr()->orX();
            foreach ((array) $criteria->get('source') as $value) {
                $orX->add($articleSourcesQueryBuilder->expr()->eq('articleSource.name', $articleSourcesQueryBuilder->expr()->literal($value)));
            }
            $articleSourcesQueryBuilder->andWhere($orX);
            $queryBuilder->andWhere($queryBuilder->expr()->in('a.id', $articleSourcesQueryBuilder->getQuery()->getDQL()));

            $criteria->remove('source');
        }

        if ($criteria->has('author') && !empty($criteria->get('author'))) {
            $orX = $queryBuilder->expr()->orX();
            foreach ((array) $criteria->get('author') as $value) {
                $orX->add($queryBuilder->expr()->eq('au.name', $queryBuilder->expr()->literal($value)));
            }

            $queryBuilder->andWhere($orX);
            $criteria->remove('author');
        }

        if ($criteria->has('exclude_author') && !empty($criteria->get('exclude_author'))) {
            $andX = $queryBuilder->expr()->andX();
            foreach ((array) $criteria->get('exclude_author') as $value) {
                $andX->add($queryBuilder->expr()->neq('au.name', $queryBuilder->expr()->literal($value)));
            }

            $queryBuilder->andWhere($andX);
            $criteria->remove('author');
        }
    }
}
