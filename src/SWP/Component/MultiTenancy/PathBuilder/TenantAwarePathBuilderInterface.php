<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\MultiTenancy\PathBuilder;

/**
 * The Tenant Aware Path Builder responsibility is to build
 * PHPCR base paths which are tenant aware.
 */
interface TenantAwarePathBuilderInterface
{
    /**
     * Builds tenant aware PHPCR base paths.
     * This method adds tenant subdomain name after the root path (/swp)
     * (e.g. /swp/tenant1/...).
     *
     * Usage: $pathBuilder->build($data);
     *
     * @param string|array $data    An array of base paths.
     * @param string       $context The absolute path context to make path absolute if needed.
     *
     * @return string|array The normalized tenant aware path or false.
     */
    public function build($data, $context = null);
}
