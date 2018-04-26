<?php
/**
 * Created by PhpStorm.
 * User: rafal
 * Date: 23.04.2018
 * Time: 13:23.
 */

namespace SWP\Bundle\CoreBundle\WebSocket\Topic;

use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

final class PackageTopic implements TopicInterface, PushableTopicInterface
{
    /**
     * {@inheritdoc}
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $topic->broadcast(['msg' => sprintf('%d connected to %s', $connection->resourceId, $topic->getId())]);
    }

    /**
     * {@inheritdoc}
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $topic->broadcast(['msg' => sprintf('%d connected to %s', $connection->resourceId, $topic->getId())]);
    }

    /**
     * {@inheritdoc}
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onPush(Topic $topic, WampRequest $request, $data, $provider)
    {
        $topic->broadcast($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'swp.package';
    }
}
