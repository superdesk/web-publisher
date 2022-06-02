<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use SWP\Bundle\ContentBundle\Doctrine\RelatedArticleRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RelatedArticleRepository extends EntityRepository implements RelatedArticleRepositoryInterface
{
    public function getByCriteria(Criteria $criteria, array $sorting): array
    {
        $queryBuilder = $this->createQueryBuilder('ra');

        $this->applyCustomCriteria($queryBuilder, $criteria);
        $this->applyCriteria($queryBuilder, $criteria, 'ra');
        $this->applySorting($queryBuilder, $sorting, 'ra', $criteria);
        $this->applyLimiting($queryBuilder, $criteria);

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByCriteria(Criteria $criteria): int
    {
        $queryBuilder = $this->createQueryBuilder('ra')
            ->select('COUNT(ra.id)');

        $this->applyCustomCriteria($queryBuilder, $criteria);
        $this->applyCriteria($queryBuilder, $criteria, 'ra');

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getPaginatedByCriteria(EventDispatcherInterface $eventDispatcher, Criteria $criteria, array $sorting = [], PaginationData $paginationData = null): PaginationInterface
    {
        $queryBuilder = $this->getQueryByCriteria($criteria, $sorting, 'ra');
        $this->applyCustomCriteria($queryBuilder, $criteria);

        if (null === $paginationData) {
            $paginationData = new PaginationData();
        }

        return $this->getPaginator($eventDispatcher,$queryBuilder, $paginationData);
    }

    private function applyCustomCriteria(QueryBuilder $queryBuilder, Criteria $criteria): void
    {
        if ($criteria->has('article')) {
            $article = $criteria->get('article');
            $queryBuilder
                ->where('ra.relatesTo = :article')
                ->setParameter('article', $article);

            $criteria->remove('article');
        }
    }
}
