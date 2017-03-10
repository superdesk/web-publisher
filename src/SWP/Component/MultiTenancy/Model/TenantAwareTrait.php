<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\MultiTenancy\Model;

trait TenantAwareTrait
{
    /**
     * @var string
     */
    protected $tenantCode;

    /**
     * {@inheritdoc}
     */
    public function getTenantCode()
    {
        return $this->tenantCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setTenantCode($code)
    {
        $this->tenantCode = $code;
    }
}
