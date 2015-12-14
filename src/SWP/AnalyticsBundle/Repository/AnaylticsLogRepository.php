<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\AnalyticsBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AnalyticsLogRepository extends EntityRepository
{
    /**
     * Find the latest logs
     */
    public function findLatest()
    {
        $qb = $this->createQueryBuilder('l');

        $qb->add('orderBy', 'l.id DESC');

        $qb->setMaxResults(200);

        //Get our query
        $q = $qb->getQuery();

        //Return result
        return $q->getResult();
    }
}
