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

use SWP\Component\Common\Criteria\Criteria;
use SWP\Component\ContentList\Model\ContentListInterface;

interface ContentListItemRepositoryInterface
{
    /**
     * @param ContentListInterface $contentList
     */
    public function removeItems(ContentListInterface $contentList);

    /**
     * @param Criteria $criteria
     * @param array    $sorting
     * @param array    $groupValues
     *
     * @return mixed
     */
    public function getSortedItems(Criteria $criteria, array $sorting = [], array $groupValues = []);
}
