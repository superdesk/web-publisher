<?php

declare(strict_types=1);

namespace SWP\Component\Paywall\Model;

interface PaywallSecuredInterface
{
    public function isPaywallSecured(): bool;

    public function setPaywallSecured(bool $paywallSecured): void;
}
