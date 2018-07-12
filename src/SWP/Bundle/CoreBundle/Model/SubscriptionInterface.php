<?php

declare(strict_types=1);

namespace SWP\Bundle\CoreBundle\Model;

use SWP\Component\Paywall\Model\SubscriptionInterface as BaseSubscriptionInterface;

interface SubscriptionInterface extends BaseSubscriptionInterface
{
    public function getUser(): ?UserInterface;

    public function setUser(?UserInterface $user): void;
}
