<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2019 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Provider;

use const DIRECTORY_SEPARATOR;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

final class CachedThemeAssetProvider extends ThemeAssetProvider implements CachedThemeAssetProviderInterface
{
    private $cacheDir;

    public function __construct(FilesystemInterface $filesystem, string $cacheDir)
    {
        parent::__construct($filesystem);

        $this->cacheDir = $cacheDir;
    }

    public function readFile(string $filePath): string
    {
        return $this->getAndCache($filePath);
    }

    public function getCachedFileLocation(string $path): ?string
    {
        $cacheFilePath = $this->getCacheDirectoryPath($path);
        $localFilesystem = new SymfonyFilesystem();
        if ($localFilesystem->exists($cacheFilePath)) {
            return $cacheFilePath;
        }

        return null;
    }

    private function getAndCache(string $path): ?string
    {
        $cacheFilePath = $this->getCacheDirectoryPath($path);
        $localFilesystem = new SymfonyFilesystem();
        if ($localFilesystem->exists($cacheFilePath)) {
            return file_get_contents($cacheFilePath);
        }

        $fileContent = parent::readFile($path);
        $localFilesystem->dumpFile($cacheFilePath, $fileContent);

        return $fileContent;
    }

    private function getCacheDirectoryPath(string $path)
    {
        return $this->cacheDir.DIRECTORY_SEPARATOR.'s3'.DIRECTORY_SEPARATOR.$path;
    }
}
