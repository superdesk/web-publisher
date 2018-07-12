<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Provider;

use SWP\Component\Paywall\Model\SubscriberInterface;

interface SubscriptionsProviderInterface
{
    public function getSubscriptions(SubscriberInterface $subscriber, array $filters = []): array;
}
