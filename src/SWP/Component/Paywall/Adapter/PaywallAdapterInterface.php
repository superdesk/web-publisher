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

use SWP\Component\Paywall\Model\SubscriberInterface;
use SWP\Component\Paywall\Model\SubscriptionInterface;

interface PaywallAdapterInterface
{
    public function getSubscriptions(SubscriberInterface $subscriber, array $filters = []): array;

    public function getSubscription(SubscriberInterface $subscriber, array $filters = []): ?SubscriptionInterface;
}
