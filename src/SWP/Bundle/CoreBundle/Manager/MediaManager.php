<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Manager;

use SWP\Bundle\ContentBundle\Manager\MediaManager as BaseMediaManager;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;

class MediaManager extends BaseMediaManager
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * @param TenantContextInterface $tenantContext
     */
    public function setTenantContext(TenantContextInterface $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    /**
     * @return string
     */
    protected function getMediaBasePath(): string
    {
        $tenant = $this->tenantContext->getTenant();
        $pathElements = ['swp', $tenant->getOrganization()->getCode(), 'media'];

        return implode('/', $pathElements);
    }
}
