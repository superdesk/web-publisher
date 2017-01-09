<?php

declare(strict_types=1);

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

use Doctrine\ORM\QueryBuilder;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

/**
 * Container Repository.
 */
class WidgetModelRepository extends EntityRepository
{
    /**
     * Get Query for WidgetModel searched by id.
     *
     * @param string $id
     *
     * @return QueryBuilder
     */
    public function getById($id): QueryBuilder
    {
        $qb = $this->createQueryBuilder('w')
            ->where('w.id = :id')
            ->setParameters([
                'id' => $id,
            ]);

        return $qb;
    }

    /**
     * Get Query for all WidgetModels.
     *
     * @return QueryBuilder
     */
    public function getAll(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('w');

        return $qb;
    }
}
