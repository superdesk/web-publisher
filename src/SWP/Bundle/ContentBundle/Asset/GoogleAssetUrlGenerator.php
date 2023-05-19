<?php

namespace SWP\Bundle\ContentBundle\Asset;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use SWP\Bundle\ContentBundle\Model\FileInterface;

class GoogleAssetUrlGenerator implements AssetUrlGeneratorInterface
{
    protected Bucket $bucket;

    public function __construct(Bucket $bucket)
    {
        $this->bucket = $bucket;
    }

    public function generateUrl(FileInterface $file, string $basePath): string
    {
        $key = $basePath . DIRECTORY_SEPARATOR . $file->getAssetId() . '.' . $file->getFileExtension();
        return $this->bucket->object($key)->gcsUri();
    }
}