<?php

declare(strict_types=1);

namespace SWP\Behat\Service;

use SWP\Component\GeoIP\Reader\ReaderInterface;

class GeoIPReaderAdapterStub implements ReaderInterface
{
    public function getCountry(string $ipAddress): string
    {
        return 'United States';
    }

    public function getState(string $ipAddress): string
    {
        return 'Texas';
    }
}
