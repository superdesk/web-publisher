<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Locator;

use League\Flysystem\FilesystemInterface;
use SWP\Bundle\CoreBundle\Detection\DeviceDetectionInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

class ApplicationResourceLocator implements ResourceLocatorInterface
{
    private $filesystem;

    private $deviceDetection;

    public function __construct(FilesystemInterface $filesystem, DeviceDetectionInterface $deviceDetection)
    {
        $this->filesystem = $filesystem;
        $this->deviceDetection = $deviceDetection;
    }

    public function locateResource(string $resourceName, ThemeInterface $theme): string
    {
        $paths = $this->getApplicationPaths($resourceName, $theme);
        foreach ($paths as $path) {
            if ($this->filesystem->has($path)) {
                return $path;
            }
        }

        throw new ResourceNotFoundException($resourceName, $theme);
    }

    protected function getApplicationPaths(string $resourceName, ThemeInterface $theme): array
    {
        $paths = [sprintf('%s/%s', $theme->getPath(), $resourceName)];
        if (null !== $this->deviceDetection->getType()) {
            $paths[] = sprintf('%s/%s/%s', $theme->getPath(), $this->deviceDetection->getType(), $resourceName);
            krsort($paths);
        }

        return $paths;
    }
}
