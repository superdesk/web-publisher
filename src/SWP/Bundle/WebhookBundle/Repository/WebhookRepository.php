<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Webhook Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\WebhookBundle\Repository;

use Doctrine\ORM\Query;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class WebhookRepository extends EntityRepository implements WebhookRepositoryInterface
{
    public function getEnabledForEvent(string $event): Query
    {
        $queryBuilder = $this->createQueryBuilder('w');
        $like = $queryBuilder->expr()->like('w.events', $queryBuilder->expr()->literal('%'.$event.'%'));

        return $queryBuilder
            ->where('w.enabled = true')
            ->andWhere($like)
            ->getQuery();
    }
}
