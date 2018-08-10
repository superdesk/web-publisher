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

use SWP\Bundle\ContentBundle\Doctrine\SlideshowRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Criteria\Criteria;

class SlideshowRepository extends EntityRepository implements SlideshowRepositoryInterface
{
    public function getByCriteria(Criteria $criteria, array $sorting): array
    {
        $qb = $this->getQueryByCriteria($criteria, $sorting, 's');

        // get items

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByCriteria(Criteria $criteria): int
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)');

        $this->applyCriteria($queryBuilder, $criteria, 's');

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
