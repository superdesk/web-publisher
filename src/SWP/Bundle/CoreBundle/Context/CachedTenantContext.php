<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Context;

use Doctrine\Common\Cache\Cache;
use SWP\Bundle\MultiTenancyBundle\Context\TenantContext;
use SWP\Component\MultiTenancy\Resolver\TenantResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CachedTenantContext extends TenantContext
{
    /**
     * CachedTenantContext constructor.
     *
     * @param TenantResolverInterface  $tenantResolver
     * @param RequestStack             $requestStack
     * @param EventDispatcherInterface $dispatcher
     * @param Cache                    $cacheProvider
     */
    public function __construct(
        TenantResolverInterface $tenantResolver,
        RequestStack $requestStack,
        EventDispatcherInterface $dispatcher,
        Cache $cacheProvider
    ) {
        $this->tenantResolver = $tenantResolver;
        $this->requestStack = $requestStack;
        $this->dispatcher = $dispatcher;
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getTenant()
    {
        if (null === $this->tenant) {
            $currentRequest = $this->requestStack->getCurrentRequest();
            if (null !== $currentRequest) {
                $cacheKey = md5($currentRequest->getHost());
                if ($this->cacheProvider->contains($cacheKey)) {
                    $this->setTenant($this->cacheProvider->fetch($cacheKey));
                } else {
                    $tenant = $this->tenantResolver->resolve(
                        $currentRequest ? $currentRequest->getHost() : null
                    );
                    $this->cacheProvider->save($cacheKey, $tenant);
                    $this->setTenant($tenant);
                }
            }
        }

        return $this->tenant;
    }
}
