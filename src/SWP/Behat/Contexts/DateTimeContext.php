<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use SWP\Behat\Service\DateTimeService;

class DateTimeContext implements Context
{
    private $dateTimeService;

    public function __construct(DateTimeService $dateTimeService)
    {
        $this->dateTimeService = $dateTimeService;
    }

    /**
     * @Given the current date time is :dateTime
     */
    public function theCurrentTimeIs(string $dateTime): void
    {
        $this->dateTimeService->setCurrentDateTime(new \DateTime($dateTime, new \DateTimeZone('UTC')));
    }
}
