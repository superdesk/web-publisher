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

namespace SWP\Bundle\CoreBundle\Tests\Adapter;

use SWP\Bundle\FixturesBundle\WebTestCase;
use SWP\Component\Paywall\Adapter\PaymentsHubAdapter;
use SWP\Component\Paywall\Adapter\PaywallAdapterInterface;
use SWP\Component\Paywall\Model\SubscriberInterface;
use SWP\Component\Paywall\Model\SubscriptionInterface;

final class PaymentsHubAdapterTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->initDatabase();
        $this->loadCustomFixtures(['tenant', 'user']);
    }

    public function testGettingSubscriberSubscriptions(): void
    {
        /** @var PaywallAdapterInterface $paymentsHubAdapter */
        $paymentsHubAdapter = $this->getContainer()->get(PaymentsHubAdapter::class);

        $subscriber = $this->getSubscriber();

        $subscriptions = $paymentsHubAdapter->getSubscriptions($subscriber);

        self::assertCount(1, $subscriptions);
        self::assertInstanceOf(SubscriptionInterface::class, $subscriptions[0]);
    }

    public function testGettingSubscriptionsByCriteria(): void
    {
        /** @var PaywallAdapterInterface $paymentsHubAdapter */
        $paymentsHubAdapter = $this->getContainer()->get(PaymentsHubAdapter::class);

        $subscriber = $this->getSubscriber();

        $subscriptions = $paymentsHubAdapter->getSubscriptions($subscriber, [
            'articleId' => 12,
        ]);

        self::assertCount(1, $subscriptions);
        self::assertInstanceOf(SubscriptionInterface::class, $subscriptions[0]);
        self::assertEquals('12', $subscriptions[0]->getId());

        $subscriptionDetails = $subscriptions[0]->getDetails();

        self::assertEquals('12', $subscriptionDetails['articleId']);
        self::assertEquals('premium_content', $subscriptionDetails['name']);
        self::assertEquals('test.user@sourcefabric.org', $subscriptionDetails['email']);

        $subscriptions = $paymentsHubAdapter->getSubscriptions($subscriber, [
            'routeId' => 30,
        ]);

        self::assertCount(1, $subscriptions);
        self::assertInstanceOf(SubscriptionInterface::class, $subscriptions[0]);
        self::assertEquals('14', $subscriptions[0]->getId());

        $subscriptionDetails = $subscriptions[0]->getDetails();

        self::assertEquals('30', $subscriptionDetails['routeId']);
        self::assertEquals('20', $subscriptionDetails['articleId']);
        self::assertEquals('secured', $subscriptionDetails['name']);
        self::assertEquals('test.user@sourcefabric.org', $subscriptionDetails['email']);
    }

    public function testGetSubscription(): void
    {
        /** @var PaywallAdapterInterface $paymentsHubAdapter */
        $paymentsHubAdapter = $this->getContainer()->get(PaymentsHubAdapter::class);

        $subscriber = $this->getSubscriber();

        $subscription = $paymentsHubAdapter->getSubscription($subscriber);

        self::assertInstanceOf(SubscriptionInterface::class, $subscription);
    }

    public function testGetSubscriptionByCriteria(): void
    {
        /** @var PaywallAdapterInterface $paymentsHubAdapter */
        $paymentsHubAdapter = $this->getContainer()->get(PaymentsHubAdapter::class);

        $subscriber = $this->getSubscriber();

        $subscription = $paymentsHubAdapter->getSubscription($subscriber, [
            'routeId' => 30,
            'articleId' => 12,
        ]);

        self::assertInstanceOf(SubscriptionInterface::class, $subscription);

        self::assertEquals('14', $subscription->getId());

        $subscriptionDetails = $subscription->getDetails();

        self::assertEquals('30', $subscriptionDetails['routeId']);
        self::assertEquals('12', $subscriptionDetails['articleId']);
        self::assertEquals('premium_content', $subscriptionDetails['name']);
        self::assertEquals('test.user@sourcefabric.org', $subscriptionDetails['email']);
    }

    private function getSubscriber(): SubscriberInterface
    {
        $userRepository = $this->getContainer()->get('swp.repository.user');
        $subscriber = $userRepository->findOneBy(['email' => 'test.user@sourcefabric.org']);

        return $subscriber;
    }
}
