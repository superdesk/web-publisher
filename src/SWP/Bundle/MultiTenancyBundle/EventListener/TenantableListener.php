<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\MultiTenancyBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
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
     * Construct.
     *
     * @param EntityManagerInterface $entityManager
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(EntityManagerInterface $entityManager, TenantContextInterface $tenantContext)
    {
        $this->entityManager = $entityManager;
        $this->tenantContext = $tenantContext;
    }

    /**
     * Enables tenantable filter on kernel.request.
     */
    public function onKernelRequest()
    {
        $tenant = $this->tenantContext->getTenant();
        if ($tenant && $tenant->getId()) {
            $tenantId = $tenant->getId();
            $this->entityManager
                ->getFilters()
                ->enable('tenantable')
                ->setParameter('tenantId', $tenantId);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
