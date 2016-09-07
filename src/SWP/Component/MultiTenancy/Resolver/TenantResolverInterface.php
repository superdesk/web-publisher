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

namespace SWP\Component\MultiTenancy\Resolver;

interface TenantResolverInterface
{
    const DEFAULT_TENANT = 'default';

    /**
     * Resolves the tenant based on current host.
     *
     * @param string|null $host The current hostname
     *
     * @return \SWP\Component\MultiTenancy\Model\TenantInterface
     */
    public function resolve($host = null);
}
