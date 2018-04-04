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
use SWP\Bundle\ContentBundle\Doctrine\ArticleAuthorRepositoryInterface;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class ArticleAuthorRepository extends EntityRepository implements ArticleAuthorRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByCriteria(Criteria $criteria, array $sorting): array
    {
        $qb = $this->getQueryByCriteria($criteria, $sorting, 'a');

        $this->applyCustomFiltering($criteria, $qb);

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function countByCriteria(Criteria $criteria): int
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)');

        $this->applyCriteria($queryBuilder, $criteria, 'a');

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    private function applyCustomFiltering(Criteria $criteria, QueryBuilder $queryBuilder)
    {
        foreach (['jobtitle'] as $name) {
            if (!$criteria->has($name)) {
                continue;
            }

            if (!\is_array($criteria->get($name))) {
                $criteria->remove($name);

                continue;
            }

            $orX = $queryBuilder->expr()->orX();
            foreach ($criteria->get($name) as $value) {
                $valueExpression = $queryBuilder->expr()->literal('%'.$value.'%');
                $orX->add($queryBuilder->expr()->like('a.jobTitle', $valueExpression));
            }

            $queryBuilder->andWhere($orX);
            $criteria->remove($name);
        }

        $properties = array_merge($this->getClassMetadata()->getFieldNames(), $this->getClassMetadata()->getAssociationNames());
        foreach ($criteria as $key => $criterion) {
            if (!\in_array(\str_replace('exclude_', '', $key), $properties, true)) {
                continue;
            }

            if ($criteria->has($key) && !empty($criteria->get($key))) {
                $orX = $queryBuilder->expr()->orX();
                foreach ((array) $criteria->get($key) as $value) {
                    $eqKey = 'a.'.str_replace('exclude_', '', $key);
                    $eqValue = $queryBuilder->expr()->literal($value);
                    if (false !== strpos($key, 'exclude_')) {
                        $orX->add($queryBuilder->expr()->neq($eqKey, $eqValue));
                    } else {
                        $orX->add($queryBuilder->expr()->eq($eqKey, $eqValue));
                    }
                }

                $queryBuilder->andWhere($orX);
                $criteria->remove($key);
            }
        }

        return $queryBuilder;
    }
}
