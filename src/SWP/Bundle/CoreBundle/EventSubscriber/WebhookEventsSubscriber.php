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

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use SWP\Bundle\ContentBundle\Event\ArticleEvent;
use SWP\Bundle\ContentBundle\Event\RouteEvent;
use SWP\Bundle\CoreBundle\Model\WebhookInterface;
use SWP\Bundle\WebhookBundle\Repository\WebhookRepositoryInterface;
use SWP\Bundle\CoreBundle\Webhook\WebhookEvents;
use SWP\Bundle\MultiTenancyBundle\Context\TenantContext;
use SWP\Component\Common\Serializer\SerializerInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

final class WebhookEventsSubscriber extends AbstractWebhookEventSubscriber
{
    private $producer;

    private $serializer;

    public function __construct(
        ProducerInterface $producer,
        SerializerInterface $serializer,
        WebhookRepositoryInterface $webhooksRepository,
        TenantContext $tenantContext,
        TenantRepositoryInterface $tenantRepository
    ) {
        $this->producer = $producer;
        $this->serializer = $serializer;

        parent::__construct($webhooksRepository, $tenantContext, $tenantRepository);
    }

    public static function getSubscribedEvents(): array
    {
        $subscribedEvents = [];
        foreach (WebhookEvents::EVENTS as $webhookEvent) {
            $subscribedEvents[$webhookEvent] = 'handleEvent';
        }

        return $subscribedEvents;
    }

    public function handleEvent(Event $event, string $dispatcherEventName, EventDispatcherInterface $dispatcher): void
    {
        $webhookEventName = $this->getEventName($event);
        if (!is_string($webhookEventName)) {
            return;
        }

        $subject = $this->getSubject($event);
        $webhooks = $this->getWebhooks($subject, $webhookEventName, $dispatcher);

        if (0 === \count($webhooks)) {
            return;
        }

        $serializedSubject = $this->serializer->serialize($subject, 'json');
        /** @var WebhookInterface $webhook */
        foreach ($webhooks as $webhook) {
            $payload = $this->serializer->serialize([
                'url' => $webhook->getUrl(),
                'metadata' => [
                    'event' => $webhookEventName,
                    'tenant' => $webhook->getTenantCode(),
                ],
                'subject' => '___SUBJECT___',
            ], 'json');

            // Do not serialize subject in a loop - serialize it before and inject in string here.
            $payload = str_replace('"___SUBJECT___"', $serializedSubject, $payload);
            $this->producer->publish($payload);
        }
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
