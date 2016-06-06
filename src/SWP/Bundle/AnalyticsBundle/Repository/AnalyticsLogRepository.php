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
namespace SWP\Bundle\AnalyticsBundle\Repository;

class AnalyticsLogRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find the latest logs.
     */
    public function getLatest($maxResults = 20)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->add('orderBy', 'l.modified DESC');
        $qb->setMaxResults($maxResults);

        return $qb->getQuery();
    }

    /**
     * Find the latest logs byUri.
     */
    public function getLatestByUri($uri, $maxResults = 20)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.uri = :uri')
            ->setParameters([
                'uri' => $uri,
            ]);
        $qb->add('orderBy', 'l.modified DESC');
        $qb->setMaxResults($maxResults);

        return $qb->getQuery();
    }
}
