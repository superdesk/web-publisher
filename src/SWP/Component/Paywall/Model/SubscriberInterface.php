<?php

declare(strict_types=1);

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
