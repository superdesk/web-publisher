<?php

declare(strict_types=1);

namespace SWP\Bundle\GeoIPBundle\Reader;

interface ReaderInterface
{
    public function getCountry(string $ipAddress): string;

    public function getState(string $ipAddress): string;
}
