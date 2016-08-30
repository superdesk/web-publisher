<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\MultiTenancy\Repository;

use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\Storage\Repository\RepositoryInterface;

/**
 * Repository interface for tenants.
 */
interface TenantRepositoryInterface extends RepositoryInterface
{
    /**
     * Finds the tenant by subdomain.
     *
     * @param string $subdomain The subdomain
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return TenantInterface|null The instance of TenantInterface or null
     */
    public function findOneBySubdomain($subdomain);

    /**
     * Finds the tenant by code.
     *
     * @param string $code The code
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return TenantInterface|null The instance of TenantInterface or null
     */
    public function findOneByCode($code);

    /**
     * Finds all available tenants.
     *
     * @return array An array of tenants
     */
    public function findAvailableTenants();
}
