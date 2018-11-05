<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\CoreBundle\Model\WebhookInterface;
use SWP\Bundle\CoreBundle\Repository\WebhookRepositoryInterface;
use SWP\Bundle\CoreBundle\Webhook\WebhookEvents;
use SWP\Bundle\MultiTenancyBundle\Context\TenantContext;
use SWP\Component\MultiTenancy\Model\TenantAwareInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

final class WebhookEventsSubscriber implements EventSubscriberInterface
{
    private $producer;

    private $serializer;

    private $webhooksRepository;

    private $tenantContext;

    private $tenantRepository;

    public function __construct(
        ProducerInterface $producer,
        SerializerInterface $serializer,
        WebhookRepositoryInterface $webhooksRepository,
        TenantContext $tenantContext,
        TenantRepositoryInterface $tenantRepository
    ) {
        $this->producer = $producer;
        $this->serializer = $serializer;
        $this->webhooksRepository = $webhooksRepository;
        $this->tenantContext = $tenantContext;
        $this->tenantRepository = $tenantRepository;
    }

    public static function getSubscribedEvents(): array
    {
        $subscribedEvents = [];
        foreach (WebhookEvents::EVENTS as $webhookEvent) {
            $subscribedEvents[$webhookEvent] = 'handleEvent';
        }

        return $subscribedEvents;
    }

    public function handleEvent(Event $event): void
    {
        $eventName = $this->getEventName($event);
        if (!is_string($eventName)) {
            return;
        }

        $subject = $this->getSubject($event);
        $webhooks = $this->getWebhooks($event, $subject);

        /** @var WebhookInterface $webhook */
        foreach ($webhooks as $webhook) {
            $this->producer->publish($this->serializer->serialize([
                'url' => $webhook->getUrl(),
                'metadata' => [
                    'event' => $this->getEventName($event),
                    'tenant' => $webhook->getTenantCode(),
                ],
                'subject' => $subject,
            ], 'json'));
        }
    }

    private function getWebhooks(Event $event, $subject): array
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
        }

        $webhooks = $this->webhooksRepository->getEnabledForEvent($this->getEventName($event))->getResult();

        if (null !== $originalTenant) {
            $this->tenantContext->setTenant($originalTenant);
        }

        return $webhooks;
    }

    private function getEventName(Event $event): ?string
    {
        if ($event instanceof GenericEvent) {
            $arguments = $event->getArguments();
            if (array_key_exists('eventName', $arguments)) {
                return array_search($arguments['eventName'], WebhookEvents::EVENTS);
            }
        } elseif (method_exists($event, 'getEventName')) {
            return array_search($event->getEventName(), WebhookEvents::EVENTS);
        }

        return null;
    }

    private function getSubject(Event $event)
    {
        switch ($event) {
            case $event instanceof GenericEvent:
                return $event->getSubject();
            case $event instanceof ArticleEvent:
                return $event->getArticle();
            case $event instanceof RouteEvent:
                return $event->getRoute();
            default:
                return null;
        }
    }
}
