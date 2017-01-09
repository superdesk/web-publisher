<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Revision\Repository;

use Doctrine\ORM\QueryBuilder;
use SWP\Component\Common\Criteria\Criteria;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Revision\Model\Revision;

class RevisionRepository extends EntityRepository implements RevisionRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPublishedRevision(): QueryBuilder
    {
        $qb = $this->getQueryByCriteria(new Criteria(), [], 'r')
            ->where('r.status = :status')
            ->setParameter('status', Revision::STATE_PUBLISHED)
            ->orderBy('r.publishedAt', 'desc');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkingRevision(): QueryBuilder
    {
        $qb = $this->getQueryByCriteria(new Criteria(), [], 'r')
            ->where('r.status = :status')
            ->setParameter('status', Revision::STATE_NEW);

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
