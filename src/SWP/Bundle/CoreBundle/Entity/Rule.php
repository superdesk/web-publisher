<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Entity;

use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use SWP\Component\Rule\Model\Rule as BaseRule;

class Rule extends BaseRule implements TenantAwareInterface, PersistableInterface
{
    protected $tenantCode;

    /**
     * Gets the current tenant (code).
     *
     * @return string Tenant code
     */
    public function getTenantCode()
    {
        return $this->tenantCode;
    }

    /**
     * Sets the tenant (code).
     *
     * @param string $code Tenant code
     */
    public function setTenantCode($code)
    {
        $this->tenantCode = $code;
    }
}
