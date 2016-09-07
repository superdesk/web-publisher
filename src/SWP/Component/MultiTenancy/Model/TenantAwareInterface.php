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

namespace SWP\Component\MultiTenancy\Model;

/**
 * TenantAwareInterface should be implemented by classes that depends on the Tenant.
 */
interface TenantAwareInterface
{
    /**
     * Gets the current tenant (code).
     *
     * @return string Tenant code
     */
    public function getTenantCode();

    /**
     * Sets the tenant (code).
     *
     * @param string $code Tenant code
     */
    public function setTenantCode($code);
}
