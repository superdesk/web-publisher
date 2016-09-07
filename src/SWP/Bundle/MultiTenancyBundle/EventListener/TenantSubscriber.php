<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;

/**
 * Doctrine listener used to set tenant before the persist.
 */
class TenantSubscriber implements EventSubscriber
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
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
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
    protected function addTenant(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof TenantAwareInterface) {
            // skip when tenant is already set
            if (null !== $entity->getTenantCode()) {
                return;
            }

            $entity->setTenantCode($this->tenantContext->getTenant()->getCode());
        }
    }
}
