<?php

/**
 * This file is part of the Superdesk Web Publisher Web Renderer Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\WebRendererBundle\Theme\Asset;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Asset\Installer\AssetsInstaller as BaseAssetsInstaller;

class AssetsInstaller extends BaseAssetsInstaller
{
    const ASSETS_DIRECTORY = '/public';

    /**
     * {@inheritdoc}
     */
    public function installAssets($targetDir, $symlinkMask)
    {
        $effectiveSymlinkMask = parent::installAssets($targetDir, $symlinkMask);

        $this->installGlobalAssets($targetDir, $symlinkMask);

        return $effectiveSymlinkMask;
    }

    /**
     * @param string $targetDir
     * @param int    $symlinkMask
     *
     * @return mixed
     */
    protected function installGlobalAssets($targetDir, $symlinkMask)
    {
        $targetDir = rtrim($targetDir, '/').'/bundles/';
        $effectiveSymlinkMask = $symlinkMask;

        foreach ($this->themeRepository->findAll() as $theme) {
            $themes = $this->themeHierarchyProvider->getThemeHierarchy($theme);

            foreach ($this->findGlobalAssetsPaths($themes) as $originDir) {
                $effectiveSymlinkMask = min(
                    $effectiveSymlinkMask,
                    $this->installThemedBundleAssets($theme, $originDir, $targetDir, $symlinkMask)
                );
            }
        }

        return $effectiveSymlinkMask;
    }

    /**
     * {@inheritdoc}
     */
    protected function installThemedBundleAssets(ThemeInterface $theme, $originDir, $targetDir, $symlinkMask)
    {
        $this->filesystem->remove($this->pathResolver->resolve($targetDir, $theme));

        return parent::installThemedBundleAssets($theme, $originDir, $targetDir, $symlinkMask);
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
}
