<?php

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

namespace SWP\Bundle\CoreBundle\Theme\Loader;

use Exception;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Twig\Loader\ExistsLoaderInterface;
use Twig\Loader\LoaderInterface;
use Twig\Source;

final class FilesystemTemplateLoader implements LoaderInterface, ExistsLoaderInterface
{
    private $decoratedLoader;

    private $templateLocator;

    private $templateNameParser;

    /** @var string[] */
    private $cache = [];

    private $filesystem;

    private $cacheDir;

    public function __construct(
        LoaderInterface $decoratedLoader,
        FileLocatorInterface $templateLocator,
        TemplateNameParserInterface $templateNameParser,
        FilesystemInterface $filesystem,
        string $cacheDir
    ) {
        $this->decoratedLoader = $decoratedLoader;
        $this->templateLocator = $templateLocator;
        $this->templateNameParser = $templateNameParser;
        $this->filesystem = $filesystem;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContext($name): Source
    {
        try {
            $path = $this->findTemplate($name);

            return new Source($this->getAndCache($path), (string) $name, $path);
        } catch (Exception $exception) {
            return $this->decoratedLoader->getSourceContext($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name): string
    {
        try {
            return $this->findTemplate($name);
        } catch (Exception $exception) {
            return $this->decoratedLoader->getCacheKey($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time): bool
    {
        try {
            return filemtime($this->findTemplate($name)) <= $time;
        } catch (Exception $exception) {
            return $this->decoratedLoader->isFresh($name, $time);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name): bool
    {
        try {
            return false !== stat($this->findTemplate($name));
        } catch (Exception $exception) {
            return $this->decoratedLoader->exists($name);
        }
    }

    private function findTemplate($logicalName): string
    {
        $logicalName = (string) $logicalName;

        if (isset($this->cache[$logicalName])) {
            return $this->cache[$logicalName];
        }

        $template = $this->templateNameParser->parse($logicalName);

        /** @var string $file */
        $file = $this->templateLocator->locate($template);

        return $this->cache[$logicalName] = $file;
    }

    private function getAndCache(string $path): string
    {
        $cacheFilePath = $this->cacheDir.DIRECTORY_SEPARATOR.'s3'.DIRECTORY_SEPARATOR.$path;
        $localFilesystem = new SymfonyFilesystem();
        if ($localFilesystem->exists($cacheFilePath)) {
            return file_get_contents($cacheFilePath);
        }

        $fileContent = $this->filesystem->read($path);
        $localFilesystem->dumpFile($cacheFilePath, $fileContent);

        return $fileContent;
    }
}
