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

namespace SWP\Bundle\CoreBundle\Theme\Locator;

use Generator;
use InvalidArgumentException;
use League\Flysystem\Filesystem;
use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;

final class TenantS3ThemesRecursiveFileLocator extends TenantThemesRecursiveFileLocator
{
    private $filesystem;

    public function __construct(FinderFactoryInterface $finderFactory, array $paths, Filesystem $filesystem)
    {
        parent::__construct($finderFactory, $paths);

        $this->filesystem = $filesystem;
    }

    protected function doLocateFilesNamed($name): ?Generator
    {
        $this->assertNameIsNotEmpty($name);
        $found = false;
        foreach ($this->paths as $path) {
            $files = $this->filesystem->listContents($path, true);
            try {
                $filteredFiles = \array_filter($files, static function ($value) use ($name) {
                    return 'file' === $value['type'] && $value['filename'].'.'.$value['extension'] === $name;
                });

                foreach ($filteredFiles as $file) {
                    $found = true;

                    yield $file['path'];
                }
            } catch (InvalidArgumentException $exception) {
            }
        }

        if (false === $found) {
            throw new InvalidArgumentException(sprintf(
                'The file "%s" does not exist (searched in the following directories: %s).',
                $name,
                implode(', ', $this->paths)
            ));
        }
    }
}
