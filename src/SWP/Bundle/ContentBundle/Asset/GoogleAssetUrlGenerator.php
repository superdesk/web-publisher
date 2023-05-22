<?php

namespace SWP\Bundle\ContentBundle\Asset;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use SWP\Bundle\ContentBundle\Model\FileInterface;

class GoogleAssetUrlGenerator implements AssetUrlGeneratorInterface
{
    protected Bucket $bucket;

    /**
     * @param Bucket $bucket
     */
    public function __construct(Bucket $bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @param FileInterface $file
     * @param string $basePath
     * @return string
     */
    public function generateUrl(FileInterface $file, string $basePath): string
    {
        $key = $basePath . '/' . $file->getAssetId() . '.' . $file->getFileExtension();
        return 'https://storage.googleapis.com/' . $this->bucket->name() . '/' . $key;
    }
}