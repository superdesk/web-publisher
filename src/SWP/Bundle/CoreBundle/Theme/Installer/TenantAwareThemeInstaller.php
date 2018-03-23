<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Installer;

use SWP\Bundle\CoreBundle\Twig\Cache\TenantAwareCacheInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Sylius\Bundle\ThemeBundle\Asset\Installer\AssetsInstallerInterface;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TenantAwareThemeUploader.
 */
final class TenantAwareThemeInstaller implements ThemeInstallerInterface
{
    public const TRAGET_DIR = 'web';

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var ThemeLoaderInterface
     */
    private $themeLoader;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $baseDir;

    /**
     * @var AssetsInstallerInterface
     */
    private $assetsInstaller;

    /**
     * TenantAwareThemeInstaller constructor.
     *
     * @param TenantContextInterface $tenantContext
     * @param ThemeLoaderInterface   $themeLoader
     * @param \Twig_Environment      $twig
     * @param string                 $baseDir
     */
    public function __construct(
        TenantContextInterface $tenantContext,
        ThemeLoaderInterface $themeLoader,
        \Twig_Environment $twig,
        string $baseDir,
        AssetsInstallerInterface $assetsInstaller
    ) {
        $this->tenantContext = $tenantContext;
        $this->themeLoader = $themeLoader;
        $this->twig = $twig;
        $this->baseDir = $baseDir;
        $this->assetsInstaller = $assetsInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function install(string $themeName = null, $sourceDir = null, $themeDir = null): ?ThemeInterface
    {
        $theme = null;
        if (null === $sourceDir || null === $themeDir) {
            $themes = array_filter(
                $this->themeLoader->load(),
                function ($element) use (&$themeName) {
                    return $element->getName() === $themeName;
                }
            );

            if (0 === count($themes)) {
                throw new NotFoundHttpException(
                    sprintf('Theme with name "%s" was not found in organization themes.', $themeName)
                );
            }
            /** @var ThemeInterface $theme */
            $theme = reset($themes);
            $sourceDir = $theme->getPath();
            $directoryName = basename($theme->getPath());
            $themeDir = $this->getThemesPath().DIRECTORY_SEPARATOR.$directoryName;
        }

        $filesystem = new Filesystem();

        $filesystem->mirror($sourceDir, $themeDir, null, ['override' => true, 'delete' => true]);

        $cache = $this->twig->getCache();
        if ($cache instanceof TenantAwareCacheInterface) {
            $filesystem->remove($cache->generateCacheDir());
        }

        $this->assetsInstaller->installAssets(self::TRAGET_DIR, AssetsInstallerInterface::HARD_COPY);

        return $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemesPath()
    {
        if (null === $tenant = $this->tenantContext->getTenant()) {
            throw new \Exception('Tenant was not found in context!');
        }

        return $this->baseDir.DIRECTORY_SEPARATOR.$tenant->getCode();
    }
}
