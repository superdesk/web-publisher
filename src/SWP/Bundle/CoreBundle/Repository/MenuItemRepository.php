<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Repository;

use SWP\Bundle\MenuBundle\Doctrine\ORM\MenuItemRepository as BaseRepository;

class MenuItemRepository extends BaseRepository implements MenuItemRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByRoute(int $routeId): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.route = :route')
            ->setParameter('route', $routeId)
            ->getQuery()
            ->getResult();
    }
}
