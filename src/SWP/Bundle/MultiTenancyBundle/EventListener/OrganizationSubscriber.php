<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\MultiTenancyBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use SWP\Component\MultiTenancy\Model\OrganizationAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class OrganizationSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
        $this->addOrganization($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    protected function addOrganization(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof OrganizationAwareInterface) {
            // skip when organization is already set
            if (null !== $entity->getOrganization()) {
                return;
            }

            $tenantContext = $this->container->get('swp_multi_tenancy.tenant_context');
            $entity->setOrganization($tenantContext->getTenant()->getOrganization());
        }
    }
}
