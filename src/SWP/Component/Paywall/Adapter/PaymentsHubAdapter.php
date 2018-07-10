<?php

declare(strict_types=1);

namespace SWP\Component\Paywall\Adapter;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use SWP\Component\Paywall\Exception\InvalidResponseException;
use SWP\Component\Paywall\Model\SubscriberInterface;
use SWP\Component\Paywall\Model\SubscriptionInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class PaymentsHubAdapter extends AbstractPaywallAdapter
{
    public const API_ENDPOINT = '/public-api/v1/';

    public const API_AUTH_ENDPOINT = '/api/v1/login_check';

    public const ENDPOINT_SUBSCRIPTIONS = self::API_ENDPOINT.'subscriptions/';

    /**
     * @var array
     */
    private $config;

    /**
     * @var FactoryInterface
     */
    private $subscriptionFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * PaymentsHubAdapter constructor.
     *
     * @param array            $config
     * @param FactoryInterface $subscriptionFactory
     * @param ClientInterface  $client
     */
    public function __construct(array $config, FactoryInterface $subscriptionFactory, ClientInterface $client)
    {
        $this->config = $config;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->client = $client;
        // base_uri
    }

    public function getSubscription(string $subscriptionId): SubscriptionInterface
    {
        $endpoint = self::ENDPOINT_SUBSCRIPTIONS.$subscriptionId;
        $response = $this->send($endpoint);
        $subscriptionData = \json_decode($response->getBody()->getContents(), true);

        /** @var SubscriptionInterface $subscription */
        $subscription = $this->subscriptionFactory->create();
        $subscription->setCode($subscriptionData['id']);
        $subscription->setType($subscriptionData['type']);
        $subscription->setDetails($subscriptionData['metadata']);

        return $subscription;
    }

    public function getSubscriptions(SubscriberInterface $subscriber): array
    {
        $response = $this->send(self::ENDPOINT_SUBSCRIPTIONS.$subscriber->getSubscriberId());
        $subscriptionsData = \json_decode($response->getBody()->getContents(), true);

        if (!isset($subscriptionsData['_embedded']) && !isset($subscriptionsData['_embedded']['items'])) {
            throw new InvalidResponseException();
        }

        $items = $subscriptionsData['_embedded']['items'];
        $subscriptions = [];
        foreach ($items as $subscriptionData) {
            /** @var SubscriptionInterface $subscription */
            $subscription = $this->subscriptionFactory->create();
            $subscription->setCode((string) $subscriptionData['id']);
            $subscription->setType($subscriptionData['type']);
            $subscription->setDetails($subscriptionData['metadata']);

            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }

    private function send(string $endpoint, array $data = [], array $requestOptions = []): ResponseInterface
    {
        if (empty($requestOptions)) {
            $requestOptions = [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $this->getJsonSerializer()->serialize($data, 'json'),
                'timeout' => 5,
            ];
        }

        // TODO handle token refresh

        if (isset($requestOptions['headers'])) {
            $requestOptions['headers']['Authorization'] = sprintf('Bearer %s', $this->getAuthToken());
        }

        /** @var ResponseInterface $response */
        $response = $this->client->get($this->config['serverUrl'].$endpoint, $requestOptions);

        return $response;
    }

    private function getAuthToken(): string
    {
        $requestOptions['body'] = $this->getJsonSerializer()->serialize([
            'username' => $this->config['credentials']['username'],
            'password' => $this->config['credentials']['password'],
        ], 'json');

        /** @var ResponseInterface $response */
        $response = $this->client->post($this->config['serverUrl'].self::API_AUTH_ENDPOINT, $requestOptions);

        $decodedResponse = \json_decode($response->getBody()->getContents(), true);

        return $decodedResponse['token'];
    }
}
