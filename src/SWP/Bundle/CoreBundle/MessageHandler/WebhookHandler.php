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
use SWP\Bundle\CoreBundle\Webhook\Message\WebhookMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class WebhookHandler implements MessageHandlerInterface
{
    public function __invoke(WebhookMessage $webhookMessage)
    {
        $headers = ['content-type' => 'application/json'];
        if (!empty($metadata = $webhookMessage->getMetadata())) {
            foreach ($metadata as $header => $value) {
                $headers['X-WEBHOOK-'.\strtoupper($header)] = $value;
            }
        }

        $webhookRequest = new GuzzleHttp\Psr7\Request(
            'POST',
            $webhookMessage->getUrl(),
            $headers,
            $webhookMessage->getBody()
        );

        $this->getClient()->send($webhookRequest);
    }

    protected function getClient(): Client
    {
        return new Client();
    }
}
