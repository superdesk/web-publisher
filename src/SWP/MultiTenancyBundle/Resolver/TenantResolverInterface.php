<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\MultiTenancyBundle\Resolver;

interface TenantResolverInterface
{
    /**
     * Resolves the tenant based on current host.
     *
     * @param string|null $host The current hostname
     *
     * @return \SWP\MultiTenancyBundle\Model\TenantInterface
     */
    public function resolve($host = null);
}
