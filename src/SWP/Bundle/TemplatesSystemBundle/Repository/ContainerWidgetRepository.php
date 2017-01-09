<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * ContainerWidget Repository.
 */
class ContainerWidgetRepository extends SortableRepository
{
    /**
     * @param array $groupValues
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSortedWidgets(array $groupValues = [])
    {
        return parent::getBySortableGroupsQueryBuilder($groupValues)
            ->select('n', 'w')
            ->leftJoin('n.widget', 'w');
    }
}
