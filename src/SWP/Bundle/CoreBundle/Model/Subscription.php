<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Component\Paywall\Model\Subscription as BaseSubscription;

class Subscription extends BaseSubscription implements SubscriptionInterface
{
    /** @var UserInterface|null */
    protected $user;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }
}
