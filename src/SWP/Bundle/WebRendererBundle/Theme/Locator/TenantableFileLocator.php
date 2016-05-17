<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\WebRendererBundle\Theme\Locator;

use SWP\Bundle\WebRendererBundle\Theme\Helper\PathHelperInterface;
use Sylius\Bundle\ThemeBundle\Factory\FinderFactoryInterface;
use Sylius\Bundle\ThemeBundle\Locator\FileLocatorInterface;

class TenantableFileLocator implements FileLocatorInterface
{
    /**
     * @var FinderFactoryInterface
     */
    private $finderFactory;

    /**
     * @var array
     */
    private $paths;

    /**
     * @param FinderFactoryInterface $finderFactory Finder factory
     * @param array                  $paths         An array of paths where to look for resources
     * @param PathHelperInterface    $helper        Path helper
     */
    public function __construct(FinderFactoryInterface $finderFactory, array $paths, PathHelperInterface $helper)
    {
        $this->finderFactory = $finderFactory;
        $this->paths = $helper->applySuffixFor($paths);
    }

    /**
     * {@inheritdoc}
     */
    public function locateFileNamed($name)
    {
        return $this->doLocateFilesNamed($name)->current();
    }

    /**
     * {@inheritdoc}
     */
    public function locateFilesNamed($name)
    {
        return iterator_to_array($this->doLocateFilesNamed($name));
    }

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     * 
     * @return \Generator
     */
    private function doLocateFilesNamed($name)
    {
        $this->assertNameIsNotEmpty($name);
        $found = false;
        foreach ($this->paths as $path) {
            try {
                $finder = $this->finderFactory->create();
                $finder
                    ->files()
                    ->name($name)
                    ->ignoreUnreadableDirs()
                    ->in($path);

                /** @var SplFileInfo $file */
                foreach ($finder as $file) {
                    $found = true;

                    yield $file->getPathname();
                }
            } catch (\InvalidArgumentException $exception) {
            }
        }

        if (false === $found) {
            throw new \InvalidArgumentException(sprintf(
                'The file "%s" does not exist (searched in the following directories: %s).',
                $name,
                implode(', ', $this->paths)
            ));
        }
    }

    /**
     * @param string $name
     */
    private function assertNameIsNotEmpty($name)
    {
        if (null === $name || '' === $name) {
            throw new \InvalidArgumentException(
                'An empty file name is not valid to be located.'
            );
        }
    }
}
