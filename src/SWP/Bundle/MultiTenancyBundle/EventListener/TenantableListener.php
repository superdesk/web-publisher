<?php

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * TenantableListener class.
 *
 * It makes sure all SELECT queries are tenant aware.
 */
class TenantableListener implements EventSubscriberInterface
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * Construct.
     *
     * @param RegistryInterface      $doctrine
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(RegistryInterface $doctrine, TenantContextInterface $tenantContext)
    {
        $this->doctrine = $doctrine;
        $this->tenantContext = $tenantContext;
    }

    /**
     * Enables tenantable filter on kernel.request.
     */
    public function enable()
    {
        $this->lazyLoad();
        $tenant = $this->tenantContext->getTenant();

        if ($tenant && $tenant->getId()) {
            $this->entityManager
                ->getFilters()
                ->enable('tenantable')
                ->setParameter('tenantCode', $tenant->getCode());
        }
    }

    /**
     * Disabled tenantable filter.
     */
    public function disable()
    {
        $this->lazyLoad();
        $filters = $this->entityManager->getFilters();

        if ($filters->isEnabled('tenantable')) {
            $filters->disable('tenantable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'enable',
            MultiTenancyEvents::TENANTABLE_ENABLE => 'enable',
            MultiTenancyEvents::TENANTABLE_DISABLE => 'disable',
        ];
    }

    private function lazyLoad()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->doctrine->getManager();
        }
    }
}
