<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\MultiTenancy\Provider;

use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

class TenantProvider implements TenantProviderInterface
{
    private $tenantRepository;

    private $internalCache = [];

    public function __construct(TenantRepositoryInterface $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }

    public function getAvailableTenants(): array
    {
        return $this->tenantRepository
            ->findAvailableTenants();
    }

    public function findOneByCode(string $tenantCode): ?TenantInterface
    {
        if (isset($this->internalCache[$tenantCode])) {
            $this->internalCache[$tenantCode];
        }

        $tenant = $this->tenantRepository->findOneByCode($tenantCode);
        $this->internalCache[$tenantCode] = $tenant;

        return $tenant;
    }
}
