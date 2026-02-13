<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\MessageHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use SWP\Bundle\CoreBundle\Webhook\Message\WebhookMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Psr\Log\LoggerInterface;

class WebhookHandler implements MessageHandlerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(WebhookMessage $webhookMessage)
    {
        $headers = ['content-type' => 'application/json'];
        if (!empty($metadata = $webhookMessage->getMetadata())) {
            foreach ($metadata as $header => $value) {
                $headers['X-WEBHOOK-'.\strtoupper($header)] = $value;
            }
        }

        $webhookRequest = new Request(
            'POST',
            $webhookMessage->getUrl(),
            $headers,
            $webhookMessage->getBody()
        );

        try {
            $this->getClient()->send($webhookRequest);
        } catch (\Throwable $e) {
            // Do not block article publishing on webhook failures; log and continue
            $this->logger->error(
                'Failed to deliver webhook',
                [
                    'url' => $webhookMessage->getUrl(),
                    'exception' => $e->getMessage(),
                ]
            );
        }
    }

    protected function getClient(): Client
    {
        return new Client();
    }
}
