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
use SWP\Bundle\ContentBundle\Doctrine\SlideshowItemRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Criteria\Criteria;

class SlideshowItemRepository extends EntityRepository implements SlideshowItemRepositoryInterface
{
    public function getByCriteria(Criteria $criteria, array $sorting): array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        $this->applyCustomCriteria($queryBuilder, $criteria);
        $this->applyCriteria($queryBuilder, $criteria, 's');
        $this->applySorting($queryBuilder, $sorting, 's', $criteria);
        $this->applyLimiting($queryBuilder, $criteria);

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByCriteria(Criteria $criteria): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)');

        $this->applyCustomCriteria($queryBuilder, $criteria);
        $this->applyCriteria($queryBuilder, $criteria, 's');

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    private function applyCustomCriteria(QueryBuilder $queryBuilder, Criteria $criteria): void
    {
        $queryBuilder
            ->leftJoin('s.slideshow', 'sd');

        if ($criteria->has('slideshow')) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('sd.id', $queryBuilder->expr()->literal($criteria->get('slideshow')->getId())));

            $criteria->remove('slideshow');
        }

        if ($criteria->has('article')) {
            $article = $criteria->get('article');
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('sd.article', $queryBuilder->expr()->literal($article->getId())));

            $criteria->remove('article');
        }
    }
}
