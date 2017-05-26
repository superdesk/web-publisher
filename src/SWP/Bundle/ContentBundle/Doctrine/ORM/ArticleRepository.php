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
use Doctrine\ORM\Tools\Pagination\Paginator;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\ContentBundle\Doctrine\ArticleRepositoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Pagination\PaginationData;

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
            ->addSelect('m');

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
    public function findArticlesByCriteria(Criteria $criteria, array $sorting = []): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', $criteria->get('status', ArticleInterface::STATUS_PUBLISHED))
            ->leftJoin('a.media', 'm')
            ->addSelect('m');

        $this->applyCustomFiltering($queryBuilder, $criteria);
        $this->applyCriteria($queryBuilder, $criteria, 'a');
        $this->applySorting($queryBuilder, $sorting, 'a');
        $this->applyLimiting($queryBuilder, $criteria);

        $paginator = new Paginator($queryBuilder->getQuery(), true);

        return $paginator->getIterator()->getArrayCopy();
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

    private function applyCustomFiltering(QueryBuilder $queryBuilder, Criteria $criteria)
    {
        foreach (['metadata', 'author'] as $name) {
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
                if ('author' === $name) {
                    $valueExpression = $queryBuilder->expr()->literal('%"byline":"'.$value.'"%');
                }
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
    }
}
