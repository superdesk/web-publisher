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
namespace SWP\Bundle\MultiTenancyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

/**
 * Repository interface for tenants.
 */
class TenantRepository extends EntityRepository implements TenantRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findBySubdomain($subdomain)
    {
        return $this
            ->createQueryBuilder('t')
            ->where('t.subdomain = :subdomain')
            ->andWhere('t.enabled = true')
            ->setParameter('subdomain', $subdomain)
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
            ->where('t.enabled = true')
            ->getQuery()
            ->getArrayResult();
    }
}
