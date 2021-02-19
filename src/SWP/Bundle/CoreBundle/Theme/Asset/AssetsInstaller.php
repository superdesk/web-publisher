<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Asset;

use SWP\Bundle\CoreBundle\Theme\Repository\ReloadableThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Asset\Installer\AssetsInstallerInterface;
use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class AssetsInstaller implements AssetsInstallerInterface
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
     * @var ReloadableThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeHierarchyProviderInterface
     */
    private $themeHierarchyProvider;

    /**
     * @var PathResolverInterface
     */
    private $pathResolver;

    /**
     * @param Filesystem                         $filesystem
     * @param KernelInterface                    $kernel
     * @param ReloadableThemeRepositoryInterface $themeRepository
     * @param ThemeHierarchyProviderInterface    $themeHierarchyProvider
     * @param PathResolverInterface              $pathResolver
     */
    public function __construct(
        Filesystem $filesystem,
        KernelInterface $kernel,
        ReloadableThemeRepositoryInterface $themeRepository,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        PathResolverInterface $pathResolver
    ) {
        $this->filesystem = $filesystem;
        $this->kernel = $kernel;
        $this->themeRepository = $themeRepository;
        $this->themeHierarchyProvider = $themeHierarchyProvider;
        $this->pathResolver = $pathResolver;
    }

    const ASSETS_DIRECTORY = '/public';

    /**
     * {@inheritdoc}
     */
    public function installAssets(string $targetDir, int $symlinkMask): int
    {
        $targetDir = rtrim($targetDir, '/');
        $this->filesystem->mkdir($targetDir);
        $this->themeRepository->reloadThemes();
        $targetDir .= '/theme/';
        $this->installGlobalAssets($targetDir, $symlinkMask);

        $effectiveSymlinkMask = $symlinkMask;
        foreach ($this->kernel->getBundles() as $bundle) {
            $effectiveSymlinkMask = min($effectiveSymlinkMask, $this->installBundleAssets($bundle, $targetDir, $symlinkMask));
        }

        return $effectiveSymlinkMask;
    }

    /**
     * {@inheritdoc}
     */
    public function installBundleAssets(BundleInterface $bundle, string $targetDir, int $symlinkMask): int
    {
        $targetDir .= preg_replace('/bundle$/', '', strtolower($bundle->getName()));

        $this->filesystem->remove($targetDir);

        $effectiveSymlinkMask = $symlinkMask;
        foreach ($this->findAssetsPaths($bundle) as $originDir) {
            $effectiveSymlinkMask = min(
                $effectiveSymlinkMask,
                $this->installVanillaBundleAssets($originDir, $targetDir, $symlinkMask)
            );
        }

        foreach ($this->themeRepository->findAll() as $theme) {
            $themes = $this->themeHierarchyProvider->getThemeHierarchy($theme);

            foreach ($this->findAssetsPaths($bundle, $themes) as $originDir) {
                $effectiveSymlinkMask = min(
                    $effectiveSymlinkMask,
                    $this->installThemedBundleAssets($theme, $originDir, $targetDir, $symlinkMask)
                );
            }
        }

        return $effectiveSymlinkMask;
    }

    /**
     * @param string $targetDir
     * @param int    $symlinkMask
     *
     * @return mixed
     */
    private function installGlobalAssets($targetDir, $symlinkMask)
    {
        $effectiveSymlinkMask = $symlinkMask;
        foreach ($this->themeRepository->findAll() as $theme) {
            $themes = $this->themeHierarchyProvider->getThemeHierarchy($theme);
            foreach ($this->findGlobalAssetsPaths($themes) as $originDir) {
                $effectiveSymlinkMask = min($effectiveSymlinkMask, $this->installThemedBundleAssets($theme, $originDir, $targetDir, $symlinkMask));
            }
        }

        return $effectiveSymlinkMask;
    }

    /**
     * @param ThemeInterface[] $themes
     *
     * @return array
     */
    private function findGlobalAssetsPaths(array $themes = [])
    {
        $sources = [];
        foreach ($themes as $theme) {
            $sourceDir = $theme->getPath().self::ASSETS_DIRECTORY;
            if (is_dir($sourceDir)) {
                $sources[] = $sourceDir;
            }
        }

        return $sources;
    }

    /**
     * {@inheritdoc}
     */
    private function installVanillaBundleAssets($originDir, $targetDir, $symlinkMask)
    {
        $targetDir = str_replace('theme/', 'bundles/', $targetDir);

        return $this->installAsset($originDir, $targetDir, $symlinkMask);
    }

    /**
     * @param ThemeInterface $theme
     * @param string         $originDir
     * @param string         $targetDir
     * @param int            $symlinkMask
     *
     * @return int
     */
    private function installThemedBundleAssets(ThemeInterface $theme, $originDir, $targetDir, $symlinkMask)
    {
        $effectiveSymlinkMask = $symlinkMask;

        $finder = new Finder();
        $finder->sortByName()->ignoreDotFiles(false)->in($originDir);

        /** @var SplFileInfo[] $finder */
        foreach ($finder as $originFile) {
            $targetFile = $targetDir.'/'.$originFile->getRelativePathname();
            $targetFile = $this->pathResolver->resolve($targetFile, $theme);

            if (file_exists($targetFile) && AssetsInstallerInterface::HARD_COPY !== $symlinkMask) {
                continue;
            }

            $this->filesystem->mkdir(dirname($targetFile));

            $effectiveSymlinkMask = min(
                $effectiveSymlinkMask,
                $this->installAsset($originFile->getPathname(), $targetFile, $symlinkMask)
            );
        }

        return $effectiveSymlinkMask;
    }

    /**
     * @param string $origin
     * @param string $target
     * @param int    $symlinkMask
     *
     * @return int
     */
    private function installAsset($origin, $target, $symlinkMask)
    {
        if (AssetsInstallerInterface::RELATIVE_SYMLINK === $symlinkMask) {
            try {
                $targetDirname = realpath(is_dir($target) ? $target : dirname($target));
                $relativeOrigin = rtrim($this->filesystem->makePathRelative($origin, $targetDirname), '/');

                $this->doInstallAsset($relativeOrigin, $target, true);

                return AssetsInstallerInterface::RELATIVE_SYMLINK;
            } catch (IOException $exception) {
                // Do nothing, trying to create non-relative symlinks later.
            }
        }

        if (AssetsInstallerInterface::HARD_COPY !== $symlinkMask) {
            try {
                $this->doInstallAsset($origin, $target, true);

                return AssetsInstallerInterface::SYMLINK;
            } catch (IOException $exception) {
                // Do nothing, hard copy later.
            }
        }

        $this->doInstallAsset($origin, $target, false);

        return AssetsInstallerInterface::HARD_COPY;
    }

    /**
     * @param string $origin
     * @param string $target
     * @param bool   $symlink
     *
     * @throws IOException when failed to make symbolic link, if requested
     */
    private function doInstallAsset($origin, $target, $symlink)
    {
        if ($symlink) {
            $this->doSymlinkAsset($origin, $target);

            return;
        }

        $this->doCopyAsset($origin, $target);
    }

    /**
     * @param BundleInterface  $bundle
     * @param ThemeInterface[] $themes
     *
     * @return array
     */
    private function findAssetsPaths(BundleInterface $bundle, array $themes = [])
    {
        $sources = [];

        foreach ($themes as $theme) {
            $sourceDir = $theme->getPath().'/'.$bundle->getName().'/public';
            if (is_dir($sourceDir)) {
                $sources[] = $sourceDir;
            }
        }

        $sourceDir = $bundle->getPath().'/Resources/public';
        if (is_dir($sourceDir)) {
            $sources[] = $sourceDir;
        }

        return $sources;
    }

    /**
     * @param string $origin
     * @param string $target
     *
     * @throws IOException If symbolic link is broken
     */
    private function doSymlinkAsset($origin, $target)
    {
        $this->filesystem->symlink($origin, $target);

        if (!file_exists($target)) {
            throw new IOException('Symbolic link is broken');
        }
    }

    /**
     * @param string $origin
     * @param string $target
     */
    private function doCopyAsset($origin, $target)
    {
        if (is_dir($origin)) {
            $this->filesystem->mkdir($target, 0777);
            $this->filesystem->mirror($origin, $target, Finder::create()->ignoreDotFiles(false)->in($origin));

            return;
        }

        $this->filesystem->copy($origin, $target);
    }
}
