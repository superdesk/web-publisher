<?php

declare(strict_types=1);

namespace SWP\Behat\Contexts;

use Behat\Behat\Context\Context;
use SWP\Component\Common\Model\DateTime;

class DateTimeContext implements Context
{
    /**
     * @Given the current date time is :dateTime
     */
    public function theCurrentTimeIs(string $dateTime): void
    {
        DateTime::setCurrentDateTime(new \DateTime($dateTime, new \DateTimeZone('UTC')));
    }
}
