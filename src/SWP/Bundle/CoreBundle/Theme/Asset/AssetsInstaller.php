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

use League\Flysystem\Filesystem as ThemesFilesystem;
use SWP\Bundle\CoreBundle\Theme\Repository\ReloadableThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Asset\Installer\AssetsInstallerInterface;
use Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface;
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class AssetsInstaller implements AssetsInstallerInterface
{
    private $themesFilesystem;

    private $filesystem;

    private $kernel;

    private $themeRepository;

    private $themeHierarchyProvider;

    private $pathResolver;

    public function __construct(
        ThemesFilesystem $themesFilesystem,
        KernelInterface $kernel,
        ReloadableThemeRepositoryInterface $themeRepository,
        ThemeHierarchyProviderInterface $themeHierarchyProvider,
        PathResolverInterface $pathResolver
    ) {
        $this->themesFilesystem = $themesFilesystem;
        $this->filesystem = new Filesystem();
        $this->kernel = $kernel;
        $this->themeRepository = $themeRepository;
        $this->themeHierarchyProvider = $themeHierarchyProvider;
        $this->pathResolver = $pathResolver;
    }

    const ASSETS_DIRECTORY = '/public';

    /**
     * {@inheritdoc}
     */
    public function installAssets(string $targetDir, int $symlinkMask)
    {
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
    public function installBundleAssets(BundleInterface $bundle, string $targetDir, int $symlinkMask)
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
            dump($this->findGlobalAssetsPaths($themes));
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
            if ($this->themesFilesystem->has($sourceDir)) {
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
        $files = $this->themesFilesystem->listContents($originDir, true);
        /* @var SplFileInfo[] $finder */
        foreach ($files as $originFile) {
            if ('file' !== $originFile['type']) {
                continue;
            }

            $targetFile = $targetDir.'/'.str_replace($originDir.'/', '', $originFile['path']);
            $targetFile = str_replace('//', '/', $this->pathResolver->resolve($targetFile, $theme));

            if (file_exists($targetFile) && AssetsInstallerInterface::HARD_COPY !== $symlinkMask) {
                continue;
            }

            $this->filesystem->mkdir(dirname($targetFile));

            $effectiveSymlinkMask = min(
                $effectiveSymlinkMask,
                $this->installAsset($originFile['path'], $targetFile, $symlinkMask)
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
            if ($this->themesFilesystem->has($sourceDir)) {
                $sources[] = $sourceDir;
            }
        }

        $sourceDir = $bundle->getPath().'/Resources/public';
        if ($this->themesFilesystem->has($sourceDir)) {
            $sources[] = $sourceDir;
        }

        return $sources;
    }

    private function doSymlinkAsset(string $origin, string $target)
    {
        $this->filesystem->symlink($origin, $target);

        if (!file_exists($target)) {
            throw new IOException('Symbolic link is broken');
        }
    }

    private function doCopyAsset(string $origin, string $target): void
    {
        if ($this->themesFilesystem->has($origin)) {
            $this->filesystem->dumpFile($target, $this->themesFilesystem->read($origin));

            return;
        }
    }
}
