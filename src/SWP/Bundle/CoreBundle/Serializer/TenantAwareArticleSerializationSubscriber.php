<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use SWP\Bundle\CoreBundle\Model\Article;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Provider\TenantProviderInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class TenantAwareArticleSerializationSubscriber implements EventSubscriberInterface
{
    private $tenantContext;

    private $originalTenant;

    private $doctrine;

    private $dispatcher;

    private $isTenantableEnabled = true;

    private $tenantProvider;

    public function __construct(
        TenantContextInterface $tenantContext,
        TenantProviderInterface $tenantProvider,
        ManagerRegistry $doctrine,
        EventDispatcherInterface $dispatcher
    ) {
        $this->tenantContext = $tenantContext;
        $this->doctrine = $doctrine;
        $this->dispatcher = $dispatcher;
        $this->tenantProvider = $tenantProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            [
                'event' => 'serializer.post_serialize',
                'class' => Article::class,
                'method' => 'onPostSerialize',
            ],
            [
                'event' => 'serializer.pre_serialize',
                'class' => Article::class,
                'method' => 'onPreSerialize',
            ],
        ];
    }

    public function onPreSerialize(ObjectEvent $event): void
    {
        $this->isTenantableEnabled = $this->doctrine->getManager()->getFilters()->isEnabled('tenantable');
        $data = $event->getObject();
        if ($data->getTenantCode() && $this->tenantContext->getTenant()->getCode() !== $data->getTenantCode()) {
            $this->originalTenant = $this->tenantContext->getTenant();
            $tenant = $this->tenantProvider->findOneByCode($data->getTenantCode());
            if (null !== $tenant) {
                $this->tenantContext->setTenant($tenant);
            }
        }
    }

    public function onPostSerialize(ObjectEvent $event): void
    {
        if ($this->originalTenant && $this->tenantContext->getTenant() !== $this->originalTenant) {
            $this->tenantContext->setTenant($this->originalTenant);
        }

        if (!$this->isTenantableEnabled) {
            $this->dispatcher->dispatch(new GenericEvent(), MultiTenancyEvents::TENANTABLE_DISABLE);
        }
    }
}
