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

use SWP\Bundle\CoreBundle\Pagination\Paginator;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Model\ContentListItemInterface;
use SWP\Component\ContentList\Repository\ContentListItemRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\SortableEntityRepository;
use SWP\Component\Storage\Repository\RepositoryInterface;

class ContentListItemRepository extends SortableEntityRepository implements ContentListItemRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function removeItems(ContentListInterface $contentList)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->delete()
            ->where('i.contentList = :contentList')
            ->setParameter('contentList', $contentList);

        $queryBuilder->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getSortedItems(Criteria $criteria, array $sorting = [], array $groupValues = [])
    {
        $queryBuilder = parent::getBySortableGroupsQueryBuilder($groupValues);
        $this->applyCriteria($queryBuilder, $criteria, 'n');
        $queryBuilder->resetDQLPart('orderBy');
        $this->applySorting($queryBuilder, $sorting, 'n');
        $queryBuilder->addOrderBy('n.position');
        $this->applyLimiting($queryBuilder, $criteria);

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedByCriteria(Criteria $criteria, array $sorting = [], PaginationData $paginationData = null)
    {
        $queryBuilder = $this->getSortedItems($criteria, $sorting, ['contentList' => $criteria->get('contentList')]);

        if ($criteria->has('exclude_content')) {
            $excludeContent = $criteria->get('exclude_content');
            if (\is_numeric($excludeContent)) {
                $excludeContent = [$excludeContent];
            }
            $queryBuilder->andWhere($queryBuilder->expr()->notIn('n.content', $excludeContent));
        }

        if (null === $paginationData) {
            $paginator = new Paginator();

            return $paginator->paginate(
                $queryBuilder,
                $criteria->get('firstResult', 0),
                $criteria->get('maxResults', RepositoryInterface::MAX_RESULTS)
            );
        }

        return $this->getPaginator($queryBuilder, $paginationData);
    }

    public function getCountByCriteria(Criteria $criteria): int
    {
        return (int) $this->getQueryByCriteria($criteria, $criteria->get('order', []), 'n')
            ->select('COUNT(n.id)')
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getOneOrNullByPosition(Criteria $criteria, int $position): ?ContentListItemInterface
    {
        return $this->getQueryByCriteria($criteria, [], 'n')
            ->orderBy('n.position', 'DESC')
            ->andWhere('n.position = :position')->setParameter('position', $position)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
