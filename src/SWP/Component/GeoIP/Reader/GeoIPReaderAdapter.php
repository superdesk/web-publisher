<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Publisher Geo IP Component.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\GeoIP\Reader;

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
