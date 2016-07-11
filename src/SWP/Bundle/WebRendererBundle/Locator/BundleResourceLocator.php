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
use Symfony\Component\HttpKernel\KernelInterface;

class BundleResourceLocator implements ResourceLocatorInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var DeviceDetectionInterface
     */
    private $deviceDetection;

    /**
     * @param Filesystem               $filesystem
     * @param KernelInterface          $kernel
     * @param DeviceDetectionInterface $deviceDetection
     */
    public function __construct(Filesystem $filesystem, KernelInterface $kernel, DeviceDetectionInterface $deviceDetection)
    {
        $this->filesystem = $filesystem;
        $this->kernel = $kernel;
        $this->deviceDetection = $deviceDetection;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $resourcePath Eg. "@AcmeBundle/Resources/views/template.html.twig"
     */
    public function locateResource($resourcePath, ThemeInterface $theme)
    {
        $this->assertResourcePathIsValid($resourcePath);
        foreach ($this->getBundlePaths($resourcePath, $theme) as $path) {
            if ($this->filesystem->exists($path)) {
                return $path;
            }
        }

        throw new ResourceNotFoundException($resourcePath, $theme);
    }

    /**
     * @param string         $resourcePath
     * @param ThemeInterface $theme
     */
    protected function getBundlePaths($resourcePath, ThemeInterface $theme)
    {
        $bundleName = $this->getBundleNameFromResourcePath($resourcePath);
        $resourceName = $this->getResourceNameFromResourcePath($resourcePath);
        $bundles = $this->kernel->getBundle($bundleName, false);
        $paths = [];
        if (is_array($bundles)) {
            foreach ($bundles as $bundle) {
                if ($this->deviceDetection->getType() !== null) {
                    $paths[] = sprintf('%s/%s/%s/%s', $theme->getPath(), $this->deviceDetection->getType(), $bundle->getName(), $resourceName);
                }
                $paths[] = sprintf('%s/%s/%s', $theme->getPath(), $bundle->getName(), $resourceName);
            }
        }

        return $paths;
    }

    /**
     * @param string $resourcePath
     */
    private function assertResourcePathIsValid($resourcePath)
    {
        if ('@' !== substr($resourcePath, 0, 1)) {
            throw new \InvalidArgumentException(sprintf('Bundle resource path (given "%s") should start with an "@".', $resourcePath));
        }

        if (false !== strpos($resourcePath, '..')) {
            throw new \InvalidArgumentException(sprintf('File name "%s" contains invalid characters (..).', $resourcePath));
        }

        if (false === strpos($resourcePath, 'Resources/')) {
            throw new \InvalidArgumentException(sprintf('Resource path "%s" should be in bundles\' "Resources/" directory.', $resourcePath));
        }
    }

    /**
     * @param string $resourcePath
     *
     * @return string
     */
    private function getBundleNameFromResourcePath($resourcePath)
    {
        return substr($resourcePath, 1, strpos($resourcePath, '/') - 1);
    }

    /**
     * @param string $resourcePath
     *
     * @return string
     */
    private function getResourceNameFromResourcePath($resourcePath)
    {
        return substr($resourcePath, strpos($resourcePath, 'Resources/') + strlen('Resources/'));
    }
}
