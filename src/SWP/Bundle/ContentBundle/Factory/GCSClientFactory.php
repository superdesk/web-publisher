<?php

namespace SWP\Bundle\ContentBundle\Factory;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GCSClientFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $keyFilePath
     * @return StorageClient
     */
    public static function createGCSClient(ContainerInterface $container, string $keyFilePath): StorageClient
    {
        $path = $container->getParameter('kernel.project_dir') . '/config/gcs/' . $keyFilePath;
        return new StorageClient([
            'keyFilePath' => $path,
        ]);
    }

    /**
     * @param ContainerInterface $container
     * @param string $keyFilePath
     * @param string $bucket
     * @return Bucket
     */
    public static function bucket(ContainerInterface $container, string $keyFilePath, string $bucket): Bucket
    {
        return self::createGCSClient($container, $keyFilePath)->bucket($bucket);
    }
}