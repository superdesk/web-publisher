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
    public function findByTypes(array $types): array
    {
        $queryBuilder = $this->createQueryBuilder('cl');

        return $queryBuilder
            ->where($queryBuilder->expr()->in('cl.type', ':types'))
            ->setParameter('types', $types)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findListById(int $listId)
    {
        return $this->getListQueryBuilder()
            ->where('cl.id = :id')
            ->setParameter('id', $listId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findListByName(string $listName)
    {
        return $this->getListQueryBuilder()
            ->where('cl.name = :name')
            ->setParameter('name', $listName)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    private function getListQueryBuilder()
    {
        return $this->createQueryBuilder('cl');
    }
}
