<?php

declare(strict_types=1);

namespace SWP\Component\Paywall\Adapter;

use SWP\Component\Paywall\Model\SubscriberInterface;

interface PaywallAdapterInterface
{
    public function getSubscriptions(SubscriberInterface $subscriber, array $filters = []): array;
}
