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
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
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
     * @var string
     */
    private $baseDir;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * TenantAwareThemeInstaller constructor.
     *
     * @param TenantContextInterface $tenantContext
     * @param ThemeLoaderInterface   $themeLoader
     * @param \Twig_Environment      $twig
     * @param string                 $baseDir
     */
    public function __construct(TenantContextInterface $tenantContext, ThemeLoaderInterface $themeLoader, \Twig_Environment $twig, string $baseDir)
    {
        $this->tenantContext = $tenantContext;
        $this->themeLoader = $themeLoader;
        $this->twig = $twig;
        $this->baseDir = $baseDir;
    }

    /**
     * {@inheritdoc}
     */
    public function install(string $themeName)
    {
        $themes = array_filter(
            $this->themeLoader->load(),
            function ($element) use (&$themeName) {
                return $element->getName() === $themeName;
            }
        );

        if (0 === count($themes)) {
            throw new NotFoundHttpException(sprintf('Theme with name "%s" was not found in organization themes.', $themeName));
        }
        /** @var ThemeInterface $theme */
        $theme = reset($themes);

        $filesystem = new Filesystem();
        $directoryName = basename($theme->getPath());
        $filesystem->mirror($theme->getPath(), $this->getThemesPath().DIRECTORY_SEPARATOR.$directoryName);

        $cache = $this->twig->getCache();
        if ($cache instanceof TenantAwareCacheInterface) {
            $filesystem->remove($cache->generateCacheDir());
        }

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
