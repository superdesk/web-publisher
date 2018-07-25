Usage
=====

This bundle provide the services which help to interact with `PaymentsHub`_. It also allows you
to create your custom implementations to interact with different/custom subscriptions systems.

How to add a new adapter
------------------------

Adapters are used to retrieve the subscriptions data from the external subscription system. It is possible to implement your
custom adapter and use it to fetch the subscriptions data from the 3rd party subscription system.

1. Create your custom adapter class which uses GuzzleHttp client

.. code-block:: php

    // src/AcmeBundle/Adapter/CustomAdapter.php

    namespace AcmeBundle\Adapter;

    use GuzzleHttp\ClientInterface;
    use GuzzleHttp\Exception\RequestException;
    use Psr\Http\Message\ResponseInterface;
    use SWP\Component\Paywall\Factory\SubscriptionFactoryInterface;
    use SWP\Component\Paywall\Model\SubscriberInterface;
    use SWP\Component\Paywall\Model\SubscriptionInterface;

    // ...
    final class CustomAdapter implements PaywallAdapterInterface
    {
        public function __construct(array $config, SubscriptionFactoryInterface $subscriptionFactory, ClientInterface $client)
        {
            $this->config = $config;
            $this->subscriptionFactory = $subscriptionFactory;
            $this->client = $client;
        }

        public function getSubscriptions(SubscriberInterface $subscriber, array $filters = []): array
        {
            // custom logic here to get subscriptions
            // ...

            $subscription = $this->subscriptionFactory->create();

            // ...
        }

        public function getSubscription(SubscriberInterface $subscriber, array $filters = []): ?SubscriptionInterface
        {
            // custom logic here to get a single subscription
            // ...
        }

        // ...
    }

2. Register your adapter as a service

.. code-block:: yaml

    # services.yml
    AcmeBundle\Adapter\CustomAdapter:
        arguments:
            -
                serverUrl: "%env(resolve:PAYWALL_SERVER_URL)%"
                credentials:
                    username: "%env(resolve:PAYWALL_SERVER_USERNAME)%"
                    password: "%env(resolve:PAYWALL_SERVER_PASSWORD)%"
            - '@SWP\Component\Paywall\Factory\SubscriptionFactory'
            - '@GuzzleHttp\Client'

3. Enabled newly created adapter in bundle's config

.. code-block:: yaml

    # config.yml
    swp_paywall:
        adapter: AcmeBundle\Adapter\CustomAdapter


.. _PaymentsHub: https://github.com/PayHelper/payments-hub
