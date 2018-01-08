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
use Doctrine\ORM\EntityManager;
use SWP\Bundle\CoreBundle\Model\Route;
use SWP\Bundle\MultiTenancyBundle\Context\TenantContext;
use SWP\Component\MultiTenancy\Model\TenantInterface;
use SWP\Component\MultiTenancy\Resolver\TenantResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CachedTenantContext extends TenantContext implements CachedTenantContextInterface
{
    /**
     * @var Cache
     */
    protected $cacheProvider;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * CachedTenantContext constructor.
     *
     * @param TenantResolverInterface  $tenantResolver
     * @param RequestStack             $requestStack
     * @param EventDispatcherInterface $dispatcher
     * @param Cache                    $cacheProvider
     * @param EntityManager            $entityManager
     */
    public function __construct(TenantResolverInterface $tenantResolver, RequestStack $requestStack, EventDispatcherInterface $dispatcher, Cache $cacheProvider, EntityManager $entityManager)
    {
        $this->tenantResolver = $tenantResolver;
        $this->requestStack = $requestStack;
        $this->dispatcher = $dispatcher;
        $this->cacheProvider = $cacheProvider;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getTenant()
    {
        if (null === $this->tenant) {
            $currentRequest = $this->requestStack->getCurrentRequest();
            if (null !== $currentRequest) {
                $cacheKey = self::getCacheKey($currentRequest->getHost());
                if ($this->cacheProvider->contains($cacheKey)) {
                    $tenant = $this->cacheProvider->fetch($cacheKey);
                    // solution for Symfony Route heavy serialization
                    if (null !== $tenant->getHomepage()) {
                        $tenant->setHomepage($this->entityManager->find(Route::class, $tenant->getHomepage()->getId()));
                    }
                    $this->setTenant($tenant);
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

    /**
     * {@inheritdoc}
     */
    public function setTenant(TenantInterface $tenant)
    {
        parent::setTenant($tenant);
        $host = $tenant->getDomainName();
        if ($subdomain = $tenant->getSubdomain()) {
            $host = $subdomain.'.'.$host;
        }
        $this->cacheProvider->delete(self::getCacheKey($host));
    }

    /**
     * {@inheritdoc}
     */
    public static function getCacheKey($host)
    {
        return 'tenant_cache__'.$host;
    }
}
