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
namespace SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR;

use SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR\DocumentRepository;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

class TenantRepository extends DocumentRepository implements TenantRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findBySubdomain($subdomain)
    {
        return $this->findOneBy([
            'subdomain' => $subdomain,
            'enabled' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByCode($code)
    {
        return $this->findOneBy([
            'code' => $code,
            'enabled' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAvailableTenants()
    {
        return $this
            ->createQueryBuilder('t')
                ->where()
                    ->eq()->field('t.enabled')->literal(true)->end()
                ->end()
            ->getQuery()
            ->getResult();
    }
}
