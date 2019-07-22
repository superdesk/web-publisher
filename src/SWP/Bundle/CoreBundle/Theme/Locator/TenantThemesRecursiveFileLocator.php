<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Locator;

use function array_filter;
use Generator;
use InvalidArgumentException;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeAssetProviderInterface;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;
use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;

class TenantThemesRecursiveFileLocator implements FileLocatorInterface
{
    protected $finderFactory;

    protected $paths;

    protected $themeAssetProvider;

    public function __construct(
        FinderFactoryInterface $finderFactory,
        array $paths,
        ThemeAssetProviderInterface $themeAssetProvider
    ) {
        $this->finderFactory = $finderFactory;
        $this->paths = $paths;
        $this->themeAssetProvider = $themeAssetProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function locateFileNamed(string $name): string
    {
        return $this->doLocateFilesNamed($name)->current();
    }

    /**
     * {@inheritdoc}
     */
    public function locateFilesNamed(string $name): array
    {
        return iterator_to_array($this->doLocateFilesNamed($name));
    }

    /**
     * @param string $name
     *
     * @return Generator
     */
    protected function doLocateFilesNamed($name)
    {
        $this->assertNameIsNotEmpty($name);
        $found = false;
        foreach ($this->paths as $path) {
            $files = $this->themeAssetProvider->listContents($path, true);
            try {
                $filteredFiles = array_filter($files, static function ($value) use ($name) {
                    return 'file' === $value['type'] && isset($value['extension']) && $value['filename'].'.'.$value['extension'] === $name;
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

    /**
     * @param string $name
     */
    protected function assertNameIsNotEmpty($name)
    {
        if (null === $name || '' === $name) {
            throw new InvalidArgumentException(
                'An empty file name is not valid to be located.'
            );
        }
    }
}
