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

namespace SWP\Bundle\CoreBundle\WebSocket\Topic;

use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

final class PackageTopic extends AbstractSecuredTopic implements TopicInterface, PushableTopicInterface
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
