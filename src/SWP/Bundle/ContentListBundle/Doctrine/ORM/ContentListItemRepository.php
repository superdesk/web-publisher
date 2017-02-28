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

use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Repository\ContentListItemRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\SortableEntityRepository;

class ContentListItemRepository extends SortableEntityRepository implements ContentListItemRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function removeItems(ContentListInterface $contentList)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->delete()
            ->where('a.contentList = :contentList')
            ->setParameter('contentList', $contentList);

        $queryBuilder->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getSortedItems(array $groupValues = [])
    {
        return parent::getBySortableGroupsQueryBuilder($groupValues)
            ->select('n', 'w')
            ->leftJoin('n.widget', 'w');
    }
}
