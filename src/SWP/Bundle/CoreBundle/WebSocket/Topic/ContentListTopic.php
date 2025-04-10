<?php

namespace SWP\Bundle\CoreBundle\WebSocket\Topic;

use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicPeriodicTimerInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicPeriodicTimerTrait;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

final class ContentListTopic extends AbstractSecuredTopic implements TopicInterface, PushableTopicInterface, TopicPeriodicTimerInterface {

    use TopicPeriodicTimerTrait;

    /**
     * {@inheritdoc}
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request) {
        $topic->broadcast(['msg' => sprintf('%d connected to %s', $connection->resourceId, $topic->getId())]);
    }

    /**
     * {@inheritdoc}
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request) {
        $topic->broadcast(['msg' => sprintf('%d connected to %s', $connection->resourceId, $topic->getId())]);
    }

    /**
     * {@inheritdoc}
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible) {
    }

    /**
     * {@inheritdoc}
     */
    public function onPush(Topic $topic, WampRequest $request, $data, $provider): void {
        $topic->broadcast($data);
    }

    /**
     * {@inheritdoc}
     */
    public function registerPeriodicTimer(Topic $topic): void {
        $n = 1;
        $this->periodicTimer->addPeriodicTimer($this, 'ping', 5, function () use ($topic, &$n) {
            $topic->broadcast(['ping' => $n]);

            ++$n;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string {
        return 'content_list_update';
    }
}
