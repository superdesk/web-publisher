<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use SWP\Component\GeoIP\Reader\ReaderInterface;
use GeoIp2\Exception\AddressNotFoundException;

class GeoIPReaderAdapterStub implements ReaderInterface
{
    private const LOCALHOST = '127.0.0.1';

    /** @var string */
    private static $country = '';

    /** @var string */
    private static $state = '';

    public function getCountry(string $ipAddress): string
    {
        if (self::LOCALHOST === $ipAddress) {
            throw new AddressNotFoundException();
        }

        return static::$country;
    }

    public function getState(string $ipAddress): string
    {
        if (self::LOCALHOST === $ipAddress) {
            throw new AddressNotFoundException();
        }

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
