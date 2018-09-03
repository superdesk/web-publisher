<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Paywall Component.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Paywall\Adapter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use SWP\Component\Paywall\Factory\SubscriptionFactoryInterface;
use SWP\Component\Paywall\Model\SubscriberInterface;
use SWP\Component\Paywall\Model\SubscriptionInterface;

final class PaymentsHubAdapter extends AbstractPaywallAdapter
{
    public const API_ENDPOINT = '/public-api/v1/';

    public const API_AUTH_ENDPOINT = '/api/v1/login_check';

    public const ENDPOINT_SUBSCRIPTIONS = self::API_ENDPOINT.'subscriptions/';

    public const ACTIVE_SUBSCRIPTION_STATE = 'fulfilled';

    /**
     * @var array
     */
    private $config;

    /**
     * @var SubscriptionFactoryInterface
     */
    private $subscriptionFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(array $config, SubscriptionFactoryInterface $subscriptionFactory, ClientInterface $client)
    {
        $this->config = $config;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->client = $client;
    }

    public function getSubscriptions(SubscriberInterface $subscriber, array $filters = []): array
    {
        $newFilters = [];
        foreach ($filters as $key => $filter) {
            $newFilters['metadata.'.$key] = $filter;
        }

        $subscriptions = [];
        $newFilters['metadata.email'] = $subscriber->getSubscriberId();

        $url = sprintf('%s%s', self::ENDPOINT_SUBSCRIPTIONS, $this->buildQueryParams($newFilters));

        $response = $this->send($url);

        if (null === $response) {
            return [];
        }

        $subscriptionsData = \json_decode($response->getBody()->getContents(), true);

        if (!isset($subscriptionsData['_embedded']) && !isset($subscriptionsData['_embedded']['items'])) {
            return null;
        }

        $items = $subscriptionsData['_embedded']['items'];

        foreach ($items as $subscriptionData) {
            /** @var SubscriptionInterface $subscription */
            $subscription = $subscription = $this->createSubscription();
            $subscription->setId((string) $subscriptionData['id']);
            $subscription->setCode((string) $subscriptionData['id']);
            $subscription->setType($subscriptionData['type']);
            $subscription->setDetails($subscriptionData['metadata']);

            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }

    public function getSubscription(SubscriberInterface $subscriber, array $filters = []): ?SubscriptionInterface
    {
        $newFilters = [];
        foreach ($filters as $key => $filter) {
            $newFilters['metadata.'.$key] = $filter;
        }

        $filters['metadata.email'] = $subscriber->getSubscriberId();

        $url = sprintf('%s%s', self::ENDPOINT_SUBSCRIPTIONS, $this->buildQueryParams($newFilters));

        $response = $this->send($url);

        if (null === $response || (null !== $response && 404 === $response->getStatusCode())) {
            return null;
        }

        $subscriptionsData = \json_decode($response->getBody()->getContents(), true);

        if (!isset($subscriptionsData['_embedded']) && !isset($subscriptionsData['_embedded']['items'])) {
            return null;
        }

        $subscriptionData = $subscriptionsData['_embedded']['items'][0];

        /** @var SubscriptionInterface $subscription */
        $subscription = $this->createSubscription();
        $subscription->setId((string) $subscriptionData['id']);
        $subscription->setCode((string) $subscriptionData['id']);
        $subscription->setType($subscriptionData['type']);
        $subscription->setDetails($subscriptionData['metadata']);
        $subscription->setActive(self::ACTIVE_SUBSCRIPTION_STATE === $subscriptionData['state']);

        return $subscription;
    }

    private function buildQueryParams(array $filters = []): string
    {
        $criteria = [
            'criteria' => $filters,
        ];

        return '?'.http_build_query($criteria);
    }

    private function send(string $endpoint, array $data = [], array $requestOptions = []): ?ResponseInterface
    {
        if (empty($requestOptions)) {
            $requestOptions = [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $this->getJsonSerializer()->serialize($data, 'json'),
                'timeout' => 3,
            ];
        }

        if (isset($requestOptions['headers'])) {
            $requestOptions['headers']['Authorization'] = sprintf('Bearer %s', $this->getAuthToken());
        }

        $response = null;

        try {
            /** @var ResponseInterface $response */
            $response = $this->client->get($this->config['serverUrl'].$endpoint, $requestOptions);
        } catch (RequestException $requestException) {
            // ignore if request fails
        }

        return $response;
    }

    private function getAuthToken(): string
    {
        $requestOptions = [
            'body' => $this->getJsonSerializer()->serialize([
                'username' => $this->config['credentials']['username'],
                'password' => $this->config['credentials']['password'],
            ], 'json'),
        ];

        /** @var ResponseInterface $response */
        $response = $this->client->post($this->config['serverUrl'].self::API_AUTH_ENDPOINT, $requestOptions);

        $decodedResponse = \json_decode($response->getBody()->getContents(), true);

        return $decodedResponse['token'];
    }

    private function createSubscription(): SubscriptionInterface
    {
        return $this->subscriptionFactory->create();
    }
}
