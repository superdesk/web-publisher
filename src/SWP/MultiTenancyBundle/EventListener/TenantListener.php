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
namespace SWP\MultiTenancyBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use SWP\MultiTenancyBundle\Context\TenantContextInterface;
use SWP\MultiTenancyBundle\Model\TenantInterface;

final class TenantListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * Construct.
     *
     * @param EntityManagerInterface $entityManager
     * @param TenantContextInterface $tenantResolver
     */
    public function __construct(EntityManagerInterface $entityManager, TenantContextInterface $tenantContext)
    {
        $this->entityManager = $entityManager;
        $this->tenantContext = $tenantContext;
    }

    /**
     * Enable the tenantable filter on kernel.request.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $tenant = $this->tenantContext->getTenant();

        if ($tenant instanceof TenantInterface) {
            $this->entityManager
                ->getFilters()
                ->enable('tenantable')
                ->setParameter('tenantId', $tenant->getId());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
        );
    }
}
