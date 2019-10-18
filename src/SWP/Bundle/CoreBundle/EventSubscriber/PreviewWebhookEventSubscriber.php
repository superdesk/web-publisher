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

namespace SWP\Bundle\CoreBundle\EventSubscriber;

use GuzzleHttp\Client;
use SWP\Bundle\ContentBundle\ArticleEvents;
use SWP\Bundle\CoreBundle\Model\ArticlePreview;
use SWP\Bundle\WebhookBundle\Repository\WebhookRepositoryInterface;
use SWP\Bundle\CoreBundle\Webhook\WebhookEvents;
use SWP\Bundle\WebhookBundle\Model\WebhookInterface;
use SWP\Component\Common\Serializer\SerializerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class PreviewWebhookEventSubscriber extends AbstractWebhookEventSubscriber
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        SerializerInterface $serializer,
        WebhookRepositoryInterface $webhooksRepository,
        TenantContextInterface $tenantContext,
        TenantRepositoryInterface $tenantRepository
    ) {
        $this->serializer = $serializer;

        parent::__construct($webhooksRepository, $tenantContext, $tenantRepository);
    }

    public static function getSubscribedEvents()
    {
        return [
            ArticleEvents::PREVIEW => 'processEvent',
        ];
    }

    public function processEvent(GenericEvent $event, string $dispatcherEventName, EventDispatcherInterface $dispatcher): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof ArticlePreview) {
            return;
        }

        $article = $subject->getArticle();
        $webhooks = $this->getWebhooks($article, WebhookEvents::PREVIEW_EVENT, $dispatcher);
        $headers = [];

        if (!isset($webhooks[0])) {
            return;
        }

        /** @var WebhookInterface $webhook */
        $webhook = $webhooks[0];

        $metadata = [
            'event' => WebhookEvents::PREVIEW_EVENT,
            'tenant' => $webhook->getTenantCode(),
        ];

        foreach ($metadata as $header => $value) {
            $headers['X-WEBHOOK-'.\strtoupper($header)] = $value;
        }

        $client = new Client();
        $requestOptions = [
            'headers' => $headers,
            'body' => $this->serializer->serialize($article, 'json'),
        ];

        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $client->post($webhook->getUrl(), $requestOptions);
        $content = $response->getBody()->getContents();

        $content = json_decode($content, true);

        if (!isset($content['url'])) {
            return;
        }

        if ($this->isUrlValid($content['url'])) {
            $subject->setPreviewUrl($content['url']);
        }
    }

    private function isUrlValid(string $url): bool
    {
        return false !== filter_var($url, FILTER_VALIDATE_URL);
    }
}
