<?php

declare(strict_types=1);

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
