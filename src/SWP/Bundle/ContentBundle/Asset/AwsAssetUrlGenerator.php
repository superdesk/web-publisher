<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Asset;

use Aws\S3\S3Client;
use SWP\Bundle\ContentBundle\Model\FileInterface;

final class AwsAssetUrlGenerator implements AssetUrlGeneratorInterface
{
    private $awsClient;

    private $awsBucket;

    private $awsPrefix;

    public function __construct(S3Client $awsClient, string $awsBucket = null, string $awsPrefix = null)
    {
        $this->awsClient = $awsClient;
        $this->awsBucket = $awsBucket;
        $this->awsPrefix = $awsPrefix;
    }

    public function generateUrl(FileInterface $file): string
    {
        $key = ($this->awsPrefix ? $this->awsPrefix.DIRECTORY_SEPARATOR : null).
            AssetUrlGeneratorInterface::ASSET_BASE_PATH.
            DIRECTORY_SEPARATOR.
            $file->getAssetId().
            '.'.
            $file->getFileExtension()
        ;

        return $this->awsClient->getObjectUrl($this->awsBucket, $key);
    }
}
