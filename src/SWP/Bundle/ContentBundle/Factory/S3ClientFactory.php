<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Factory;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;

class S3ClientFactory
{
    public static function createS3Client(string $version, string $region, ?string $endpoint, string $key, string $secret): S3Client
    {
        $options = [
            'region' => $region,
            'version' => $version,
            'endpoint' => !empty($endpoint) ? $endpoint : null,
            'credentials' => new Credentials($key, $secret),
        ];
        
        $s3Client = new S3Client($options);

        return $s3Client;
    }
}
