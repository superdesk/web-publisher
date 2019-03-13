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
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use GuzzleHttp;

class SendWebhookConsumer implements ConsumerInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function execute(AMQPMessage $message): void
    {
        $decodedMessage = $this->serializer->deserialize($message->body, 'array', 'json');
        if (!\array_key_exists('url', $decodedMessage) || !array_key_exists('subject', $decodedMessage)) {
            return;
        }

        $headers = [];
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
        } catch (GuzzleHttp\Exception\ClientException | GuzzleHttp\Exception\ServerException $e) {
            return;
        }
    }

    protected function getClient(): Client
    {
        return new Client();
    }
}
