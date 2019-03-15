<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

class DateTimeService
{
    public static $currentDateTime;

    public function setCurrentDateTime(\DateTimeInterface $dateTime): void
    {
        self::$currentDateTime = $dateTime;
    }

    public function getCurrentDateTime(): ?\DateTimeInterface
    {
        return self::$currentDateTime;
    }
}
