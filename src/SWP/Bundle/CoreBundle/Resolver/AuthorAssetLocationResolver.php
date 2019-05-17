<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Resolver;

use SWP\Bundle\ContentBundle\Resolver\AssetLocationResolver as BaseAssetLocationResolver;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

class AuthorAssetLocationResolver extends BaseAssetLocationResolver
{
    protected $tenantContext;

    public function setTenantContext(TenantContextInterface $tenantContext): void
    {
        $this->tenantContext = $tenantContext;
    }

    /**
     * @return string
     */
    public function getMediaBasePath(): string
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->tenantContext->getTenant();

        return sprintf('swp/%s/authors', $tenant->getOrganization()->getCode());
    }
}
