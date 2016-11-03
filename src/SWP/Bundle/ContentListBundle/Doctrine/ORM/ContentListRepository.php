<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentListBundle\Doctrine\ORM;

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;

class ContentListRepository extends EntityRepository implements ContentListRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findManyByCriteria(array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('cl');

        $queryBuilder
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull($this->getPropertyName('publishedBefore', 'cl')),
                    $queryBuilder->expr()->gt($this->getPropertyName('publishedBefore', 'cl'), ':publishedAt')
                )
            )
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull($this->getPropertyName('publishedAfter', 'cl')),
                    $queryBuilder->expr()->lt($this->getPropertyName('publishedAfter', 'cl'), ':publishedAt')
                )
            )
            ->orWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull($this->getPropertyName('publishedAt', 'cl')),
                    $queryBuilder->expr()->eq($this->getPropertyName('publishedAt', 'cl'), ':publishedAt')
                )
            )
            ->andWhere(
                $queryBuilder->expr()->eq($this->getPropertyName('type', 'cl'), ':type')
            )
            ->setParameters([
                'type' => $criteria['type'],
                'publishedAt' => $criteria['publishedAt'],
            ])
        ;

        unset($criteria['publishedAt'], $criteria['type']);

        $orx = $queryBuilder->expr()->orX();

        foreach ($criteria as $key => $value) {
            if (isset($criteria[$key])) {
                $orx->add($queryBuilder->expr()->eq('cl.'.$key, $queryBuilder->expr()->literal($value)));
            }
        }

        return $queryBuilder
            ->orWhere($orx)
            ->getQuery()
            ->getResult()
        ;
    }
}
