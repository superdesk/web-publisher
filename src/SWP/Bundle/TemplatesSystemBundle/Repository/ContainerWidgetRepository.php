<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Templates System Bundle.
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
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepositoryTrait;
use SWP\Component\TemplatesSystem\Repository\ContainerWidgetRepositoryInterface;

/**
 * ContainerWidget Repository.
 */
class ContainerWidgetRepository extends SortableRepository implements ContainerWidgetRepositoryInterface
{
    use EntityRepositoryTrait;

    /**
     * {@inheritdoc}
     */
    public function getSortedWidgets(array $groupValues = [])
    {
        return parent::getBySortableGroupsQueryBuilder($groupValues)
            ->select('n', 'w')
            ->leftJoin('n.widget', 'w');
    }
}
