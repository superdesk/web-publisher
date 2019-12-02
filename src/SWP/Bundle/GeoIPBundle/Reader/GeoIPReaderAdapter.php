<?php

namespace SWP\Bundle\GeoIPBundle\Reader;

use GeoIp2\Database\Reader;

class GeoIPReaderAdapter implements ReaderInterface
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getCountry(string $ipAddress): string
    {
        $record = $this->reader->city($ipAddress);

        return $record->country->name;
    }

    public function getState(string $ipAddress): string
    {
        $record = $this->reader->city($ipAddress);

        return $record->mostSpecificSubdivision->name;
    }
}
