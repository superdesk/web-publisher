<?php

declare(strict_types=1);

namespace SWP\Component\Paywall\Model;

use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface SubscriptionInterface extends PersistableInterface, TimestampableInterface, SoftDeletableInterface
{
    public function getCode(): ?string;

    public function setCode(?string $code): void;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getDetails(): array;

    public function setDetails(array $details): void;
}
