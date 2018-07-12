<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use SWP\Bundle\UserBundle\Model\User as BaseUser;
use SWP\Component\MultiTenancy\Model\OrganizationAwareTrait;
use SWP\Component\Paywall\Model\SubscriptionInterface;

class User extends BaseUser implements UserInterface
{
    use OrganizationAwareTrait;

    /**
     * @var Collection
     */
    protected $subscriptions;

    /**
     * {@inheritdoc}
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_READER) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = $this->roles;
        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }
        // we need to make sure to have at least one role
        $roles[] = static::ROLE_READER;

        return array_unique($roles);
    }

    public function getSubscriberId(): string
    {
        return $this->email;
    }

    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function setSubscriptions(Collection $subscriptions): void
    {
        $this->subscriptions = $subscriptions;
    }

    public function hasSubscription(SubscriptionInterface $subscription): bool
    {
        return $this->subscriptions->contains($subscription);
    }

    public function addSubscription(SubscriptionInterface $subscription): void
    {
        if (!$this->hasSubscription($subscription)) {
            $this->subscriptions->add($subscription);
        }
    }

    public function removeSubscription(SubscriptionInterface $subscription): void
    {
        if ($this->hasSubscription($subscription)) {
            $this->subscriptions->removeElement($subscription);
        }
    }
}
