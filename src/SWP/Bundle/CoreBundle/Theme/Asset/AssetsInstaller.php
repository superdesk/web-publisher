<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Theme\Asset;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Asset\Installer\OutputAwareAssetsInstaller;

class AssetsInstaller extends OutputAwareAssetsInstaller
{
    const ASSETS_DIRECTORY = '/public';

    /**
     * {@inheritdoc}
     */
    public function installAssets($targetDir, $symlinkMask)
    {
        $targetDir = rtrim($targetDir, '/').'/theme/';

        $this->installGlobalAssets($targetDir, $symlinkMask);

        $effectiveSymlinkMask = $symlinkMask;
        foreach ($this->kernel->getBundles() as $bundle) {
            $effectiveSymlinkMask = min($effectiveSymlinkMask, $this->installBundleAssets($bundle, $targetDir, $symlinkMask));
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
                $effectiveSymlinkMask = min(
                    $effectiveSymlinkMask,
                    $this->installThemedBundleAssets($theme, $originDir, $targetDir, $symlinkMask)
                );
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
    protected function installVanillaBundleAssets($originDir, $targetDir, $symlinkMask)
    {
        $targetDir = str_replace('theme/', 'bundles/', $targetDir);

        return parent::installVanillaBundleAssets($originDir, $targetDir, $symlinkMask);
    }
}
