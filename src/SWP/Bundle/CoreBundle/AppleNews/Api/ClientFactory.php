<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\AppleNews\Api;

use GuzzleHttp\Client;

final class ClientFactory
{
    public const BASE_URI = 'https://news-api.apple.com';

    public function create(): Client
    {
        return new Client([
            'base_uri' => self::BASE_URI,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }
}
