<?php

/*
 * This file is part of the Superdesk Publisher Revision Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\RevisionBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Revision\Model\RevisionInterface;
use SWP\Component\Revision\Repository\RevisionRepositoryInterface;

class RevisionRepository extends EntityRepository implements RevisionRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPublishedRevision(): QueryBuilder
    {
        $qb = $this->getQueryByCriteria(new Criteria(), [], 'r')
            ->where('r.status = :status')
            ->setParameter('status', RevisionInterface::STATE_PUBLISHED)
            ->orderBy('r.publishedAt', 'desc')
            ->setMaxResults(1);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkingRevision(): QueryBuilder
    {
        $qb = $this->getQueryByCriteria(new Criteria(), [], 'r')
            ->where('r.status = :status')
            ->setParameter('status', RevisionInterface::STATE_NEW)
            ->setMaxResults(1);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getByKey($key): QueryBuilder
    {
        $qb = $this->getQueryByCriteria(new Criteria(), [], 'r')
            ->where('r.uniqueKey = :key')
            ->setParameter('key', $key);

        return $qb;
    }
}
