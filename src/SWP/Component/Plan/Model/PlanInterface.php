<?php

declare(strict_types=1);

namespace SWP\Component\Plan\Model;

use SWP\Component\Common\Model\EnableableInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Storage\Model\PersistableInterface;

interface PlanInterface extends PersistableInterface, EnableableInterface, TimestampableInterface
{
    public const INTERVAL_DAY = 'day';

    public const INTERVAL_MONTH = 'month';

    public const INTERVAL_YEAR = 'year';

    /**
     * @return null|string
     */
    public function getCode(): ?string;

    /**
     * @param null|string $code
     */
    public function setCode(?string $code): void;

    /**
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void;

    /**
     * @return int
     */
    public function getAmount(): int;

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void;

    /**
     * @return string
     */
    public function getInterval(): string;

    /**
     * @param string $interval
     */
    public function setInterval(string $interval): void;

    /**
     * @return int
     */
    public function getIntervalCount(): int;

    /**
     * @param int $intervalCount
     */
    public function setIntervalCount(int $intervalCount): void;

    /**
     * @return null|string
     */
    public function getCurrency(): ?string;

    /**
     * @param null|string $currency
     */
    public function setCurrency(?string $currency): void;
}
