<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\ContentList\Repository;

use Doctrine\ORM\QueryBuilder;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\Common\Pagination\PaginationData;
use SWP\Component\ContentList\Model\ContentListInterface;
use SWP\Component\ContentList\Model\ContentListItemInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

interface ContentListItemRepositoryInterface extends RepositoryInterface
{
    public function removeItems(ContentListInterface $contentList);

    public function getSortedItems(Criteria $criteria, array $sorting = [], array $groupValues = []): QueryBuilder;

    public function getPaginatedByCriteria(Criteria $criteria, array $sorting = [], PaginationData $paginationData = null);

    public function getCountByCriteria(Criteria $criteria): int;

    public function getOneOrNullByPosition(Criteria $criteria, int $position): ?ContentListItemInterface;
}
