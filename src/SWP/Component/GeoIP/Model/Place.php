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

namespace SWP\Component\GeoIP\Model;

class Place
{
    /** @var string */
    private $country;

    /** @var string */
    private $state;

    public function __construct(string $country = '', string $state = '')
    {
        $this->country = $country;
        $this->state = $state;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function toArray(): array
    {
        return [
            'country' => $this->country,
            'state' => $this->state,
        ];
    }
}
