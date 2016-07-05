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
namespace SWP\Bundle\WebRendererBundle\Locator;

use SWP\Bundle\WebRendererBundle\Detection\DeviceDetectionInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Filesystem\Filesystem;

class ApplicationResourceLocator implements ResourceLocatorInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var DeviceDetectionInterface
     */
    private $deviceDetection;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem, DeviceDetectionInterface $deviceDetection)
    {
        $this->filesystem = $filesystem;
        $this->deviceDetection = $deviceDetection;
    }

    /**
     * {@inheritdoc}
     */
    public function locateResource($resourceName, ThemeInterface $theme)
    {
        $paths = $this->getApplicationPaths($resourceName, $theme);
        foreach ($paths as $path) {
            if ($this->filesystem->exists($path)) {
                return $path;
            }
        }

        throw new ResourceNotFoundException($resourceName, $theme);
    }

    /**
     * @param string         $resourceName
     * @param ThemeInterface $theme
     *
     * @return array
     */
    protected function getApplicationPaths($resourceName, ThemeInterface $theme)
    {
        $paths = [sprintf('%s/%s', $theme->getPath(), $resourceName)];
        if ($this->deviceDetection->getType() !== null) {
            $paths[] = sprintf('%s/%s/%s', $theme->getPath(), $this->deviceDetection->getType(), $resourceName);
            krsort($paths);
        }

        return $paths;
    }
}
