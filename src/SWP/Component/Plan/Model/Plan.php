<?php

declare(strict_types=1);

namespace SWP\Component\Plan\Model;

use SWP\Component\Common\Model\EnableableTrait;
use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;

class Plan implements PlanInterface
{
    use EnableableTrait, TimestampableTrait, SoftDeletableTrait;

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
    protected $name;

    /**
     * @var int
     */
    protected $amount = 0;

    /**
     * @var string
     */
    protected $interval = PlanInterface::INTERVAL_MONTH;

    /**
     * @var int
     */
    protected $intervalCount = 1;

    /**
     * @var string|null
     */
    protected $currency;

    /**
     * Plan constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval(): string
    {
        return $this->interval;
    }

    /**
     * {@inheritdoc}
     */
    public function setInterval(string $interval): void
    {
        $this->interval = $interval;
    }

    /**
     * {@inheritdoc}
     */
    public function getIntervalCount(): int
    {
        return $this->intervalCount;
    }

    /**
     * {@inheritdoc}
     */
    public function setIntervalCount(int $intervalCount): void
    {
        $this->intervalCount = $intervalCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }
}
