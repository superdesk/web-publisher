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
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use SWP\Bundle\CoreBundle\Model\WebhookInterface;
use SWP\Bundle\CoreBundle\Repository\WebhookRepositoryInterface;
use SWP\Bundle\CoreBundle\Webhook\WebhookEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class WebhookEventsSubscriber.
 */
class WebhookEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var ProducerInterface
     */
    protected $producer;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var WebhookRepositoryInterface
     */
    protected $webhooksRepository;

    /**
     * WebhookEventsSubscriber constructor.
     *
     * @param ProducerInterface          $producer
     * @param SerializerInterface        $serializer
     * @param WebhookRepositoryInterface $webhooksRepository
     */
    public function __construct(ProducerInterface $producer, SerializerInterface $serializer, WebhookRepositoryInterface $webhooksRepository)
    {
        $this->producer = $producer;
        $this->serializer = $serializer;
        $this->webhooksRepository = $webhooksRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        $subscribedEvents = [];
        foreach (WebhookEvents::EVENTS as $webhookEvent) {
            $subscribedEvents[$webhookEvent] = 'handleEvent';
        }

        return $subscribedEvents;
    }

    /**
     * @param Event $event
     */
    public function handleEvent(Event $event)
    {
        $webhooks = $this->webhooksRepository->getEnabledForEvent($this->getEventName($event))->getResult();

        /** @var WebhookInterface $webhook */
        foreach ($webhooks as $webhook) {
            dump($webhook, $this->getEventName($event), $this->getSubject($event), $this->serializer->serialize(['url' => $webhook->getUrl(), 'subject' => $this->getSubject($event)], 'json'));
            $this->producer->publish($this->serializer->serialize(['url' => $webhook->getUrl(), 'subject' => $this->getSubject($event)], 'json'));
        }
    }

    /**
     * @param Event $event
     *
     * @return string|null
     */
    protected function getEventName(Event $event)
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

    /**
     * @param Event $event
     *
     * @return PackageInterface|\SWP\Bundle\ContentBundle\Model\ArticleInterface|\SWP\Bundle\ContentBundle\Model\RouteInterface|null
     */
    protected function getSubject(Event $event)
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
