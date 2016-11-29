<?php

declare(strict_types=1);

/*
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

namespace SWP\Bundle\CoreBundle\Checker;

use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Takeit\Bundle\AmpHtmlBundle\Checker\AmpSupportCheckerInterface;

final class AmpSupportChecker implements AmpSupportCheckerInterface
{
    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(TenantContextInterface $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->tenantContext->getTenant();

        return $tenant->isAmpEnabled();
    }
}
