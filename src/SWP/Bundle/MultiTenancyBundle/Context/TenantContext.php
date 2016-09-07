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

namespace SWP\Bundle\MultiTenancyBundle\Context;

use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Resolver\TenantResolverInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class TenantContext.
 */
class TenantContext implements TenantContextInterface
{
    /**
     * @var TenantInterface
     */
    protected $tenant;

    /**
     * @var TenantResolverInterface
     */
    protected $tenantResolver;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * TenantContext constructor.
     *
     * @param TenantResolverInterface $tenantResolver
     * @param RequestStack            $requestStack
     */
    public function __construct(TenantResolverInterface $tenantResolver, RequestStack $requestStack)
    {
        $this->tenantResolver = $tenantResolver;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getTenant()
    {
        if (null === $this->tenant) {
            $currentRequest = $this->requestStack->getCurrentRequest();
            $this->tenant = $this->tenantResolver->resolve(
                $currentRequest ? $currentRequest->getHost() : null
            );
        }

        return $this->tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function setTenant(TenantInterface $tenant)
    {
        $this->tenant = $tenant;
    }
}
