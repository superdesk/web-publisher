<?php

declare(strict_types=1);

namespace SWP\Component\Paywall\Adapter;

use SWP\Component\Paywall\Model\SubscriberInterface;
use SWP\Component\Paywall\Model\SubscriptionInterface;

interface PaywallAdapterInterface
{
    public function getSubscription(string $subscriptionId): SubscriptionInterface;

    public function getSubscriptions(SubscriberInterface $subscriber): array;
}
