<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\Doctrine\ORM;

use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\MultiTenancy\Repository\OrganizationRepositoryInterface;

class OrganizationRepository extends EntityRepository implements OrganizationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCode($code)
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findAvailable()
    {
        return $this
            ->createQueryBuilder('o')
            ->where('o.enabled = true')
            ->getQuery()
            ->getArrayResult();
    }
}
