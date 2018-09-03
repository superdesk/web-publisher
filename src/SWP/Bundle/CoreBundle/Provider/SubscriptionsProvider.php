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

namespace SWP\Bundle\CoreBundle\Provider;

use SWP\Component\Paywall\Adapter\PaywallAdapterInterface;
use SWP\Component\Paywall\Model\SubscriberInterface;
use SWP\Component\Paywall\Model\SubscriptionInterface;

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

    public function getSubscription(SubscriberInterface $subscriber, array $filters = []): ?SubscriptionInterface
    {
        return $this->paywallAdapter->getSubscription($subscriber, $filters);
    }
}
