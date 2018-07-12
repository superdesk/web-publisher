<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Provider;

use SWP\Component\Paywall\Adapter\PaywallAdapterInterface;
use SWP\Component\Paywall\Model\SubscriberInterface;

final class SubscriptionsProvider implements SubscriptionsProviderInterface
{
    /** @var PaywallAdapterInterface */
    private $paywallAdapter;

    public function __construct(PaywallAdapterInterface $paywallAdapter)
    {
        $this->paywallAdapter = $paywallAdapter;
    }

    public function getSubscriptions(SubscriberInterface $subscriber, array $filters = []): array
    {
        return $this->paywallAdapter->getSubscriptions($subscriber, $filters);
    }
}
