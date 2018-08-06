<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use SWP\Bundle\ContentBundle\Doctrine\SlideshowRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Common\Criteria\Criteria;

class SlideshowRepository extends EntityRepository implements SlideshowRepositoryInterface
{
    public function getByCriteria(Criteria $criteria, array $sorting): array
    {
        $qb = $this->getQueryByCriteria($criteria, $sorting, 's')
            ->leftJoin('s.items', 'i');

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
