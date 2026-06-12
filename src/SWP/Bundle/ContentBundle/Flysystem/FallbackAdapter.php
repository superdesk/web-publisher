<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2026 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2026 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Flysystem;

use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;

/**
 * Flysystem 3 replacement for litipk/flysystem-fallback-adapter: reads are
 * attempted on the main adapter and fall back to the secondary one; all
 * writes go to the main adapter.
 */
class FallbackAdapter implements FilesystemAdapter
{
    private FilesystemAdapter $mainAdapter;

    private FilesystemAdapter $fallbackAdapter;

    public function __construct(FilesystemAdapter $mainAdapter, FilesystemAdapter $fallbackAdapter)
    {
        $this->mainAdapter = $mainAdapter;
        $this->fallbackAdapter = $fallbackAdapter;
    }

    public function fileExists(string $path): bool
    {
        return $this->mainAdapter->fileExists($path) || $this->fallbackAdapter->fileExists($path);
    }

    public function directoryExists(string $path): bool
    {
        return $this->mainAdapter->directoryExists($path) || $this->fallbackAdapter->directoryExists($path);
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $this->mainAdapter->write($path, $contents, $config);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->mainAdapter->writeStream($path, $contents, $config);
    }

    public function read(string $path): string
    {
        if ($this->mainAdapter->fileExists($path)) {
            return $this->mainAdapter->read($path);
        }

        return $this->fallbackAdapter->read($path);
    }

    public function readStream(string $path)
    {
        if ($this->mainAdapter->fileExists($path)) {
            return $this->mainAdapter->readStream($path);
        }

        return $this->fallbackAdapter->readStream($path);
    }

    public function delete(string $path): void
    {
        if ($this->mainAdapter->fileExists($path)) {
            $this->mainAdapter->delete($path);
        }

        if ($this->fallbackAdapter->fileExists($path)) {
            $this->fallbackAdapter->delete($path);
        }
    }

    public function deleteDirectory(string $path): void
    {
        $this->mainAdapter->deleteDirectory($path);
    }

    public function createDirectory(string $path, Config $config): void
    {
        $this->mainAdapter->createDirectory($path, $config);
    }

    public function setVisibility(string $path, string $visibility): void
    {
        $this->getAdapterForRead($path)->setVisibility($path, $visibility);
    }

    public function visibility(string $path): FileAttributes
    {
        return $this->getAdapterForRead($path)->visibility($path);
    }

    public function mimeType(string $path): FileAttributes
    {
        return $this->getAdapterForRead($path)->mimeType($path);
    }

    public function lastModified(string $path): FileAttributes
    {
        return $this->getAdapterForRead($path)->lastModified($path);
    }

    public function fileSize(string $path): FileAttributes
    {
        return $this->getAdapterForRead($path)->fileSize($path);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        yield from $this->mainAdapter->listContents($path, $deep);
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->getAdapterForRead($source)->move($source, $destination, $config);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $this->getAdapterForRead($source)->copy($source, $destination, $config);
    }

    private function getAdapterForRead(string $path): FilesystemAdapter
    {
        try {
            if ($this->mainAdapter->fileExists($path)) {
                return $this->mainAdapter;
            }
        } catch (FilesystemException $e) {
            // fall through to the fallback adapter
        }

        return $this->fallbackAdapter;
    }
}
