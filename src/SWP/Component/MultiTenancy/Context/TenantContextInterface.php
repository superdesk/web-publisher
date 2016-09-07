<?php

/**
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

namespace SWP\Component\MultiTenancy\Context;

use SWP\Component\MultiTenancy\Model\TenantInterface;

/**
 * Interface TenantContextInterface.
 */
interface TenantContextInterface
{
    /**
     * Gets current tenant.
     *
     * @return TenantInterface
     */
    public function getTenant();

    /**
     * Sets current tenant.
     *
     * @param TenantInterface $tenant
     */
    public function setTenant(TenantInterface $tenant);
}
