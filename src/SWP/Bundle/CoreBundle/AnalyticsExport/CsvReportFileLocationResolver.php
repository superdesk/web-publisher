<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\AnalyticsExport;

use SWP\Bundle\ContentBundle\Resolver\AssetLocationResolver as BaseAssetLocationResolver;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

class CsvReportFileLocationResolver extends BaseAssetLocationResolver
{
    protected $tenantContext;

    public function setTenantContext(TenantContextInterface $tenantContext): void
    {
        $this->tenantContext = $tenantContext;
    }

    public function getMediaBasePath(): string
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->tenantContext->getTenant();

        return sprintf('swp/%s/exports', $tenant->getOrganization()->getCode());
    }
}
