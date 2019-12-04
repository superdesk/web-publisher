<?php

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

declare(strict_types=1);

namespace SWP\Component\GeoIP\Model;

interface GeoIpPlaceInterface
{
    public function getGeoIpPlace(): Place;
}
