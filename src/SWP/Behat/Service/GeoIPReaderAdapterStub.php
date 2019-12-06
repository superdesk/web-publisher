<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use SWP\Component\GeoIP\Reader\ReaderInterface;

class GeoIPReaderAdapterStub implements ReaderInterface
{
    /** @var string */
    private static $country = '';

    /** @var string */
    private static $state = '';

    public function getCountry(string $ipAddress): string
    {
        return static::$country;
    }

    public function getState(string $ipAddress): string
    {
        return static::$state;
    }

    public function setCountry(string $country): void
    {
        static::$country = $country;
    }

    public function setState(string $state): void
    {
        static::$state = $state;
    }
}
