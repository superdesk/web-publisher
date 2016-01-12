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
namespace SWP\MultiTenancyBundle\Context;

use SWP\MultiTenancyBundle\Model\TenantInterface;
use SWP\MultiTenancyBundle\Resolver\TenantResolverInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use SWP\MultiTenancyBundle\Model\Tenant;

class TenantContext implements TenantContextInterface
{
    /**
     * @var TenantInterface
     */
    private $tenant;

    /**
     * @var TenantResolverInterface
     */
    private $tenantResolver;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Construct.
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
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest) {
            return new Tenant();
        }

        $host = $currentRequest->getHost();
        // TODO add caching to not resolve the hostname each time
        if (null === $this->tenant) {
            $this->tenant = $this->tenantResolver->resolve($host);
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
