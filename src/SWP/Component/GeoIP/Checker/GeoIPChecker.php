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

namespace SWP\Component\GeoIP\Checker;

use GeoIp2\Exception\AddressNotFoundException;
use SWP\Component\GeoIP\Model\Place;
use SWP\Component\GeoIP\Reader\ReaderInterface;

class GeoIPChecker
{
    /** @var ReaderInterface */
    private $reader;

    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function isGranted(string $ipAddress, Place $place): bool
    {
        try {
            $state = $place->getState();

            if ($state === $this->reader->getState($ipAddress)) {
                return false;
            }

            $country = $place->getCountry();

            if ($country === $this->reader->getCountry($ipAddress)) {
                return false;
            }

            return true;
        } catch (AddressNotFoundException $e) {
            return true;
        }
    }
}
