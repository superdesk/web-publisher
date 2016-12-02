<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Component.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\Twig;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;

class MultiTenancyExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

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
    public function getGlobals()
    {
        return [
            'tenant' => $this->tenantContext->getTenant(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'swp_multi_tenancy';
    }
}
