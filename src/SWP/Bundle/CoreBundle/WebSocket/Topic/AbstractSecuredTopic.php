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
use Gos\Bundle\WebSocketBundle\Server\Exception\FirewallRejectionException;
use Gos\Bundle\WebSocketBundle\Topic\SecuredTopicInterface;
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use SWP\Bundle\CoreBundle\Repository\ApiKeyRepositoryInterface;

abstract class AbstractSecuredTopic implements SecuredTopicInterface
{
    /**
     * @var ApiKeyRepositoryInterface
     */
    protected $apiKeyRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AbstractSecuredTopic constructor.
     *
     * @param ApiKeyRepositoryInterface $apiKeyRepository
     */
    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository, LoggerInterface $logger)
    {
        $this->apiKeyRepository = $apiKeyRepository;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function secure(ConnectionInterface $conn = null, Topic $topic, WampRequest $request, $payload = null, $exclude = null, $eligible = null, $provider = null): void
    {
        if (null === $conn) {
            return;
        }

        $httpRequest = $conn->httpRequest;
        $token = null;
        parse_str($httpRequest->getUri()->getQuery(), $params);

        if (isset($params['token'])) {
            $token = (string) $params['token'];
        }

        if (null !== $token) {
            $this->logger->info(
                'Token was found in the request',
                ['remoteAddress' => $conn->remoteAddress, 'connectionId' => $conn->resourceId]
            );

            $apiKey = $this->apiKeyRepository
                ->getValidToken($token)
                ->getQuery()
                ->getOneOrNullResult();

            if (null === $apiKey) {
                throw new FirewallRejectionException();
            }
        } else {
            $this->logger->warning(
                'Token was not found in the request',
                ['remoteAddress' => $conn->remoteAddress, 'connectionId' => $conn->resourceId]
            );
        }
    }
}
