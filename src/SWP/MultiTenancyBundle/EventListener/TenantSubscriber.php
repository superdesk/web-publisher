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

use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use SWP\MultiTenancyBundle\Model\TenantAwareInterface;
use SWP\MultiTenancyBundle\Context\TenantContextInterface;

/**
 * Doctrine listener used to set tenant before the persist.
 */
final class TenantSubscriber implements EventSubscriberInterface
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * Constructor.
     *
     * @param TenantContextInterface $tenantContext
     */
    public function __construct(TenantContextInterface $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->addTenant($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function addTenant(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof TenantAwareInterface) {
            $entity->setTenant($this->tenantContext->getTenant());
        }
    }
}
