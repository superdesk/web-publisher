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

use SWP\Bundle\CoreBundle\Detection\DeviceDetectionInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Twig\Locator\TemplateNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class BundleResourceLocator
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

    public function __construct(Filesystem $filesystem, KernelInterface $kernel, DeviceDetectionInterface $deviceDetection)
    {
        $this->filesystem = $filesystem;
        $this->kernel = $kernel;
        $this->deviceDetection = $deviceDetection;
    }

    /**
     * {@inheritdoc}
     */
    public function locate(string $resourcePath, ThemeInterface $theme): string
    {
        $this->assertResourcePathIsValid($resourcePath);

        if (false !== strpos($resourcePath, 'Bundle/Resources/views/')) {
            // When using bundle notation, we get a path like @AcmeBundle/Resources/views/template.html.twig
            return $this->locateResourceBasedOnBundleNotation($resourcePath, $theme);
        }

        // When using namespaced Twig paths, we get a path like @Acme/template.html.twig
        return $this->locateResourceBasedOnTwigNamespace($resourcePath, $theme);
    }

    private function assertResourcePathIsValid(string $resourcePath): void
    {
        if (0 !== strpos($resourcePath, '@')) {
            throw new \InvalidArgumentException(sprintf('Bundle resource path (given "%s") should start with an "@".', $resourcePath));
        }

        if (false !== strpos($resourcePath, '..')) {
            throw new \InvalidArgumentException(sprintf('File name "%s" contains invalid characters (..).', $resourcePath));
        }
    }

    private function locateResourceBasedOnBundleNotation(string $resourcePath, ThemeInterface $theme): string
    {
        $bundleName = substr($resourcePath, 1, strpos($resourcePath, '/') - 1);
        $resourceName = substr($resourcePath, strpos($resourcePath, 'Resources/') + strlen('Resources/'));

        // Symfony 4.0+ always returns a single bundle
        /** @var BundleInterface|BundleInterface[] $bundles */
        $bundles = $this->kernel->getBundle($bundleName, false);

        // So we need to hack it to support both Symfony 3.4 and Symfony 4.0+
        if (!is_array($bundles)) {
            $bundles = [$bundles];
        }

        foreach ($bundles as $bundle) {
            if (null !== $this->deviceDetection->getType()) {
                $path = sprintf('%s/%s/%s/%s', $theme->getPath(), $this->deviceDetection->getType(), $bundle->getName(), $resourceName);
                if ($this->filesystem->exists($path)) {
                    return $path;
                }
            }

            $path = sprintf('%s/%s/%s', $theme->getPath(), $bundle->getName(), $resourceName);
            if ($this->filesystem->exists($path)) {
                return $path;
            }
        }

        throw new TemplateNotFoundException($resourceName, [$theme]);
    }

    private function locateResourceBasedOnTwigNamespace(string $resourcePath, ThemeInterface $theme): string
    {
        $twigNamespace = substr($resourcePath, 1, strpos($resourcePath, '/') - 1);
        $resourceName = substr($resourcePath, strpos($resourcePath, '/') + 1);
        if (null !== $this->deviceDetection->getType()) {
            $path = sprintf('%s/%s/%s/%s', $theme->getPath(), $this->deviceDetection->getType(), $this->getBundleOrPluginName($twigNamespace), $resourceName);
            if ($this->filesystem->exists($path)) {
                return $path;
            }
        }

        $path = sprintf('%s/%s/views/%s', $theme->getPath(), $this->getBundleOrPluginName($twigNamespace), $resourceName);

        if ($this->filesystem->exists($path)) {
            return $path;
        }

        throw new TemplateNotFoundException($resourceName, [$theme]);
    }

    private function getBundleOrPluginName(string $twigNamespace): string
    {
        if ('Plugin' === substr($twigNamespace, -6)) {
            return $twigNamespace;
        }

        return $twigNamespace.'Bundle';
    }

    public function supports(string $template): bool
    {
        return strpos($template, '@') !== 0;
    }
}
