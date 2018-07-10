<?php

declare(strict_types=1);

namespace SWP\Component\Paywall\Model;

use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;

class Subscription implements SubscriptionInterface
{
    use TimestampableTrait, SoftDeletableTrait;

    /**
     * @var mixed|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $code;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var array
     */
    protected $details = [];

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): void
    {
        $this->details = $details;
    }
}
