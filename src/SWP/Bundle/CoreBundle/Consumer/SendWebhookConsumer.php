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

namespace SWP\Bundle\CoreBundle\Consumer;

use GuzzleHttp\Client;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use GuzzleHttp;
use Psr\Log\LoggerInterface;

class SendWebhookConsumer implements ConsumerInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function execute(AMQPMessage $message): int
    {
        try {
            $decodedMessage = $this->serializer->deserialize($message->body, 'array', 'json');
        } catch (RuntimeException $e) {
            $this->logger->error('Message REJECTED: '.$e->getMessage(), ['exception' => $e->getTraceAsString()]);

            return ConsumerInterface::MSG_REJECT;
        }

        if (!\array_key_exists('url', $decodedMessage) || !array_key_exists('subject', $decodedMessage)) {
            return ConsumerInterface::MSG_REJECT;
        }

        $headers = ['content-type' => 'application/json'];
        if (\array_key_exists('metadata', $decodedMessage)) {
            foreach ($decodedMessage['metadata'] as $header => $value) {
                $headers['X-WEBHOOK-'.\strtoupper($header)] = $value;
            }
        }

        $webhookRequest = new GuzzleHttp\Psr7\Request(
            'POST',
            $decodedMessage['url'],
            $headers,
            $this->serializer->serialize($decodedMessage['subject'], 'json')
        );

        try {
            $this->getClient()->send($webhookRequest);
            $this->logger->info(sprintf('Message SEND to url %s', $decodedMessage['url']), $headers);
        } catch (GuzzleHttp\Exception\ClientException | GuzzleHttp\Exception\ServerException | GuzzleHttp\Exception\ConnectException $e) {
            $this->logger->error('Message REJECTED: '.$e->getMessage(), ['exception' => $e->getTraceAsString()]);

            return ConsumerInterface::MSG_REJECT;
        }

        return ConsumerInterface::MSG_ACK;
    }

    protected function getClient(): Client
    {
        return new Client();
    }
}
