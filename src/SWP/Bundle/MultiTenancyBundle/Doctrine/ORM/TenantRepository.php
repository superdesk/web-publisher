<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\Doctrine\ORM;

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

/**
 * Repository interface for tenants.
 */
class TenantRepository extends EntityRepository implements TenantRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneBySubdomain($subdomain)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t', 'o')
            ->leftJoin('t.organization', 'o')
            ->where('t.subdomain = :subdomain')
            ->setParameter('subdomain', $subdomain)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCode($code)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t', 'o')
            ->leftJoin('t.organization', 'o')
            ->where('t.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findAvailableTenants()
    {
        return $this
            ->createQueryBuilder('t')
            ->select('t', 'o')
            ->leftJoin('t.organization', 'o')
            ->getQuery()
            ->getArrayResult();
    }
}
