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

namespace SWP\Component\Paywall\Model;

use Doctrine\Common\Collections\Collection;

interface SubscriberInterface
{
    public function getSubscriberId(): string;

    public function getSubscriptions(): Collection;

    public function setSubscriptions(Collection $subscriptions): void;

    public function hasSubscription(SubscriptionInterface $subscription): bool;

    public function addSubscription(SubscriptionInterface $subscription): void;

    public function removeSubscription(SubscriptionInterface $subscription): void;
}
