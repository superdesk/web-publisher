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
     * @var string
     */
    private $assetsDir;

    /**
     * TenantAwareThemeInstaller constructor.
     *
     * @param TenantContextInterface   $tenantContext
     * @param ThemeLoaderInterface     $themeLoader
     * @param \Twig_Environment        $twig
     * @param string                   $baseDir
     * @param AssetsInstallerInterface $assetsInstaller
     * @param string                   $assetsDir
     */
    public function __construct(
        TenantContextInterface $tenantContext,
        ThemeLoaderInterface $themeLoader,
        \Twig_Environment $twig,
        string $baseDir,
        AssetsInstallerInterface $assetsInstaller,
        string $assetsDir
    ) {
        $this->tenantContext = $tenantContext;
        $this->themeLoader = $themeLoader;
        $this->twig = $twig;
        $this->baseDir = $baseDir;
        $this->assetsInstaller = $assetsInstaller;
        $this->assetsDir = $assetsDir;
    }

    /**
     * {@inheritdoc}
     */
    public function install($sourceDir = null, $themeDir = null): void
    {
        $filesystem = new Filesystem();
        $filesystem->mirror($sourceDir, $themeDir, null, ['override' => true, 'delete' => true]);

        $cache = $this->twig->getCache();
        if ($cache instanceof TenantAwareCacheInterface) {
            $filesystem->remove($cache->generateCacheDir());
        }

        $this->assetsInstaller->installAssets($this->assetsDir, AssetsInstallerInterface::HARD_COPY);
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeFromOrganizationThemes(string $themeName): ThemeInterface
    {
        $themes = \array_filter(
            $this->themeLoader->load(),
            function ($element) use (&$themeName) {
                return $element->getName() === $themeName;
            }
        );

        if (0 === count($themes)) {
            throw new NotFoundHttpException(
                \sprintf('Theme with name "%s" was not found in organization themes.', $themeName)
            );
        }

        return \reset($themes);
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
