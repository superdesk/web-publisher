<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Service;

use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Theme\Installer\ThemeInstallerInterface;
use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;
use SWP\Bundle\CoreBundle\Theme\Processor\RequiredDataProcessorInterface;
use SWP\Bundle\CoreBundle\Theme\Repository\ReloadableThemeRepositoryInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class InstallThemeService.
 */
final class ThemeService implements ThemeServiceInterface
{
    /**
     * @var ThemeInstallerInterface
     */
    private $themeInstaller;

    /**
     * @var RequiredDataProcessorInterface
     */
    private $requiredDataProcessor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var ThemeRepositoryInterface
     */
    private $themeRepository;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * ThemeService constructor.
     *
     * @param ThemeInstallerInterface            $themeInstaller
     * @param RequiredDataProcessorInterface     $requiredDataProcessor
     * @param string                             $cacheDir
     * @param TenantContextInterface             $tenantContext
     * @param ReloadableThemeRepositoryInterface $themeRepository
     * @param ThemeContextInterface              $themeContext
     */
    public function __construct(
        ThemeInstallerInterface $themeInstaller,
        RequiredDataProcessorInterface $requiredDataProcessor,
        string $cacheDir,
        TenantContextInterface $tenantContext,
        ReloadableThemeRepositoryInterface $themeRepository,
        ThemeContextInterface $themeContext
    ) {
        $this->themeInstaller = $themeInstaller;
        $this->requiredDataProcessor = $requiredDataProcessor;
        $this->cacheDir = $cacheDir;
        $this->tenantContext = $tenantContext;
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function installAndProcessGeneratedData(string $sourceDir, string $themeDir)
    {
        $messages = [];
        /** @var TenantInterface $tenant */
        $tenant = $this->tenantContext->getTenant();
        $backupThemeDir = $this->cacheDir.\DIRECTORY_SEPARATOR.'backup'.\DIRECTORY_SEPARATOR.'themes'.\DIRECTORY_SEPARATOR.$tenant->getCode().\DIRECTORY_SEPARATOR.\basename($themeDir).'_previous';
        $fileSystem = new Filesystem();

        try {
            if ($fileSystem->exists($themeDir)) {
                $fileSystem->rename($themeDir, $backupThemeDir, true);
            }

            $this->themeInstaller->install($sourceDir, $themeDir);
            $messages[] = 'Theme has been installed successfully!';
            $this->themeRepository->reloadThemes();
            if (file_exists($themeDir.\DIRECTORY_SEPARATOR.'theme.json')) {
                $themeName = json_decode(file_get_contents($themeDir.\DIRECTORY_SEPARATOR.'theme.json'), true)['name'];
                $tenant->setThemeName($themeName);
                /** @var ThemeInterface $theme */
                $theme = $this->themeContext->getTheme();
                $this->requiredDataProcessor->processTheme($theme);
                $messages[] = 'Required data were generated and persisted successfully';
            }
        } catch (\Exception $e) {
            $fileSystem->remove($themeDir);
            if ($fileSystem->exists($backupThemeDir)) {
                $fileSystem->rename($backupThemeDir, $themeDir);
            }

            return $e;
        }

        if ($fileSystem->exists($backupThemeDir)) {
            $fileSystem->remove($backupThemeDir);
        }

        return $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectoriesForTheme(string $themeName): array
    {
        /** @var ThemeInterface $theme */
        $theme = $this->themeInstaller->getThemeFromOrganizationThemes($themeName);
        $sourceDir = $theme->getPath();
        $themeDir = $this->themeInstaller->getThemesPath().DIRECTORY_SEPARATOR.\basename($sourceDir);

        return [$sourceDir, $themeDir];
    }
}
