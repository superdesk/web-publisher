<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\MultiTenancyBundle\Doctrine\PHPCR;

/**
 * The Path Builder responsibility is to build PHPCR route paths based on root path,
 * current tenant and route name (e.g. /swp/tenant1/routes, /swp/tenant2/routes).
 */
interface TenantAwarePathBuilderInterface
{
    /**
     * Example output /swp/tenant1/routes.
     * This service should inject TenantContext.
     *
     * use https://github.com/phpcr/phpcr-utils/blob/master/src/PHPCR/Util/PathHelper.php#L194
     *
     * usage: $pathBuilder->build($pathName);
     *
     * @param string|array $path [description]
     *
     * @return [type] [description]
     */
    public function build($path);
}
