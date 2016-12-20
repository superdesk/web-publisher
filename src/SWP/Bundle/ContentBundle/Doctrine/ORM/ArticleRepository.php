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
            ->setParameter('status', $criteria->get('status', ArticleInterface::STATUS_PUBLISHED));

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function countByCriteria(Criteria $criteria): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.status = :status')
            ->setParameter('status', $criteria->get('status', ArticleInterface::STATUS_PUBLISHED));

        $this->applyCriteria($qb, $criteria, 'a');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findArticlesByCriteria(Criteria $criteria, array $sorting = [], PaginationData $paginationData = null)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', $criteria->get('status', ArticleInterface::STATUS_PUBLISHED));

        if ($criteria->has('author')) {
            foreach ($criteria->get('author') as $author) {
                $queryBuilder->andWhere($queryBuilder->expr()->like('a.metadata', ':metadata'))
                    ->setParameter('metadata', '%"'.$author.'"%');
            }
        }

        if ($criteria->has('publishedBefore')) {
            $queryBuilder->andWhere('a.publishedAt < :before')
                ->setParameter('before', $criteria->get('publishedBefore'));
        }

        if ($criteria->has('publishedAfter')) {
            $queryBuilder->andWhere('a.publishedAt > :after')
                ->setParameter('after', $criteria->get('publishedAfter'));
        }

        $this->applyCriteria($queryBuilder, $criteria, 'a');
        $this->applySorting($queryBuilder, $sorting, 'a');
        $this->applyLimiting($queryBuilder, $criteria);

        return $queryBuilder->getQuery()->getResult();
        //return $this->getPaginator($queryBuilder, $paginationData);
    }

    /**
     * @param string $identifier
     * @param array  $order
     *
     * @throws \Exception
     */
    public function getQueryForRouteArticles(string $identifier, array $order = [])
    {
        throw new \Exception('Not implemented');
    }
}
