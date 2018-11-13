<?php

declare(strict_types=1);

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
use SWP\Bundle\CoreBundle\Model\OutputChannel;
use SWP\Bundle\CoreBundle\Model\Route;
use SWP\Bundle\MultiTenancyBundle\Context\TenantContext;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\MultiTenancy\Exception\TenantNotFoundException;
use SWP\Component\MultiTenancy\Model\OrganizationInterface;
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
        $this->cacheProvider = $cacheProvider;
        $this->entityManager = $entityManager;

        parent::__construct($tenantResolver, $requestStack, $dispatcher);
    }

    /**
     * {@inheritdoc}
     */
    public function getTenant(): TenantInterface
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if ($currentRequest && $this->requestStack->getCurrentRequest()->attributes->get('exception') instanceof TenantNotFoundException) {
            return;
        }

        if (null === $this->tenant) {
            if (null !== $currentRequest) {
                $cacheKey = self::getCacheKey($currentRequest->getHost());

                if ($this->cacheProvider->contains($cacheKey) && ($tenant = $this->cacheProvider->fetch($cacheKey)) instanceof TenantInterface) {
                    // solution for serialization
                    if (null !== $tenant->getHomepage()) {
                        $tenant->setHomepage($this->entityManager->find(Route::class, $tenant->getHomepage()->getId()));
                    }
                    if (null !== $tenant->getOutputChannel()) {
                        $tenant->setOutputChannel($this->entityManager->find(OutputChannel::class, $tenant->getOutputChannel()->getId()));
                    }
                } else {
                    $tenant = $this->tenantResolver->resolve(
                        $currentRequest ? $currentRequest->getHost() : null
                    );

                    $this->cacheProvider->save($cacheKey, $tenant);
                }

                parent::setTenant($tenant);
            }
        }

        return $this->tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function setTenant(TenantInterface $tenant): void
    {
        parent::setTenant($this->attachToEntityManager($tenant));

        $host = $tenant->getDomainName();
        if ($subdomain = $tenant->getSubdomain()) {
            $host = $subdomain.'.'.$host;
        }

        $this->dispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
        $this->cacheProvider->save(self::getCacheKey($host), $tenant);
    }

    /**
     * {@inheritdoc}
     */
    public static function getCacheKey(string $host): string
    {
        return md5('tenant_cache__'.$host);
    }

    private function attachToEntityManager(TenantInterface $tenant): TenantInterface
    {
        /** @var OrganizationInterface $organization */
        $organization = $this->entityManager->merge($tenant->getOrganization());
        $tenant->setOrganization($organization);

        return  $this->entityManager->merge($tenant);
    }
}
