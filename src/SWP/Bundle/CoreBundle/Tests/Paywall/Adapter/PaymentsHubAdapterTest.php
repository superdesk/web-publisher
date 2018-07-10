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
use SWP\Component\Paywall\Model\SubscriptionInterface;

final class PaymentsHubAdapterTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->initDatabase();
        $this->loadCustomFixtures(['tenant', 'user']);
    }

    public function testGettingSubscriberSubscriptions(): void
    {
        /** @var PaywallAdapterInterface $paymentsHubAdapter */
        $paymentsHubAdapter = $this->getContainer()->get(PaymentsHubAdapter::class);

        $userRepository = $this->getContainer()->get('swp.repository.user');
        $subscriber = $userRepository->findOneBy(['email' => 'test.user@sourcefabric.org']);

        $subscriptions = $paymentsHubAdapter->getSubscriptions($subscriber);

        self::assertCount(1, $subscriptions);
        self::assertInstanceOf(SubscriptionInterface::class, $subscriptions[0]);
    }
}
