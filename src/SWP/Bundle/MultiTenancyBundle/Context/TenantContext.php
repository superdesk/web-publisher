<?php

/*
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

use SWP\Bundle\CoreBundle\Document\Tenant;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Resolver\TenantResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
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
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * TenantContext constructor.
     */
    public function __construct(TenantResolverInterface $tenantResolver, RequestStack $requestStack, EventDispatcherInterface $dispatcher)
    {
        $this->tenantResolver = $tenantResolver;
        $this->requestStack = $requestStack;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getTenant()
    {
        if (null === $this->tenant) {
            $currentRequest = $this->requestStack->getCurrentRequest();

            if (null !== $currentRequest && false !== strpos($currentRequest->getRequestUri(), '_profiler')) {
                $profilerTenant = new Tenant();
                $profilerTenant->setDomainName($currentRequest->getHost());
                $this->setTenant($profilerTenant);

                return $this->tenant;
            }

            $this->setTenant($this->tenantResolver->resolve(
                $currentRequest ? $currentRequest->getHost() : null
            ));
        }

        return $this->tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function setTenant(TenantInterface $tenant)
    {
        $this->tenant = $tenant;

        $this->dispatcher->dispatch(MultiTenancyEvents::TENANT_SET, new GenericEvent($tenant));
    }
}
