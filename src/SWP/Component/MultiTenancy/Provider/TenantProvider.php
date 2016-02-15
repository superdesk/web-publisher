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
namespace SWP\Component\MultiTenancy\Provider;

use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

class TenantProvider implements TenantProviderInterface
{
    /**
     * @var TenantRepositoryInterface
     */
    private $tenantRepository;

    /**
     * Construct.
     *
     * @param TenantRepositoryInterface $tenantRepository Tenant repository
     */
    public function __construct(TenantRepositoryInterface $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableTenants()
    {
        return $this->tenantRepository
            ->findAvailableTenants();
    }
}
