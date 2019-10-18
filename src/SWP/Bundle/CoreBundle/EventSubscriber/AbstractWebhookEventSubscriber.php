<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use SWP\Bundle\WebhookBundle\Repository\WebhookRepositoryInterface;
use SWP\Bundle\MultiTenancyBundle\MultiTenancyEvents;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractWebhookEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var WebhookRepositoryInterface
     */
    private $webhooksRepository;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var TenantRepositoryInterface
     */
    private $tenantRepository;

    public function __construct(
        WebhookRepositoryInterface $webhooksRepository,
        TenantContextInterface $tenantContext,
        TenantRepositoryInterface $tenantRepository
    ) {
        $this->webhooksRepository = $webhooksRepository;
        $this->tenantContext = $tenantContext;
        $this->tenantRepository = $tenantRepository;
    }

    public function getWebhooks($subject, string $webhookEventName, EventDispatcherInterface $dispatcher): array
    {
        $originalTenant = null;
        if (
            $subject instanceof TenantAwareInterface
            && $subject->getTenantCode() !== $this->tenantContext->getTenant()->getCode()
            && null !== $subject->getTenantCode()
            && null !== ($subjectTenant = $this->tenantRepository->findOneByCode($subject->getTenantCode()))
        ) {
            $originalTenant = $this->tenantContext->getTenant();
            $this->tenantContext->setTenant($subjectTenant);
        } else {
            $dispatcher->dispatch(MultiTenancyEvents::TENANTABLE_ENABLE);
        }

        $webhooks = $this->webhooksRepository->getEnabledForEvent($webhookEventName)->getResult();

        if (null !== $originalTenant) {
            $this->tenantContext->setTenant($originalTenant);
        }

        return $webhooks;
    }
}
