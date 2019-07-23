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

use function basename;
use const DIRECTORY_SEPARATOR;
use Exception;
use function json_decode;
use League\Flysystem\Filesystem;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Theme\Installer\ThemeInstallerInterface;
use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;
use SWP\Bundle\CoreBundle\Theme\Processor\RequiredDataProcessorInterface;
use SWP\Bundle\CoreBundle\Theme\Repository\ReloadableThemeRepositoryInterface;
use SWP\Bundle\CoreBundle\Theme\TenantAwareThemeContextInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface;

final class ThemeService implements ThemeServiceInterface
{
    private $themeInstaller;

    private $requiredDataProcessor;

    private $tenantContext;

    private $themeRepository;

    private $themeContext;

    private $tenantRepository;

    private $filesystem;

    public function __construct(
        ThemeInstallerInterface $themeInstaller,
        RequiredDataProcessorInterface $requiredDataProcessor,
        TenantContextInterface $tenantContext,
        ReloadableThemeRepositoryInterface $themeRepository,
        TenantAwareThemeContextInterface $themeContext,
        TenantRepositoryInterface $tenantRepository,
        Filesystem $filesystem
    ) {
        $this->themeInstaller = $themeInstaller;
        $this->requiredDataProcessor = $requiredDataProcessor;
        $this->tenantContext = $tenantContext;
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
        $this->tenantRepository = $tenantRepository;
        $this->filesystem = $filesystem;
    }

    public function installAndProcessGeneratedData(string $sourceDir, string $themeDir, bool $processOptionalData = false, bool $activate = false)
    {
        $messages = [];

        $this->themeInstaller->install($sourceDir, $themeDir);
        $messages[] = 'Theme has been installed successfully!';
        $this->themeRepository->reloadThemes();

        if (!$this->filesystem->has($themeDir.DIRECTORY_SEPARATOR.'theme.json')) {
            return new Exception('Theme doesn\'t have required theme.json file with configuration.');
        }

        /** @var TenantInterface $tenant */
        $tenant = $this->tenantContext->getTenant();
        $themeName = json_decode($this->filesystem->read($themeDir.DIRECTORY_SEPARATOR.'theme.json'), true)['name'];
        if ($activate) {
            $tenant->setThemeName($themeName);
            $messages[] = 'Theme was activated!';
            $this->tenantContext->setTenant($tenant);
        }

        /** @var ThemeInterface $theme */
        $theme = $this->themeRepository->findOneByName($this->themeContext->resolveThemeName($tenant, $themeName));
        $this->requiredDataProcessor->processTheme($theme, $processOptionalData);
        $this->tenantRepository->flush();
        $messages[] = 'Required data were generated and persisted successfully';
        if ($processOptionalData) {
            $messages[] = 'Optional data were generated and persisted successfully';
        }

        return $messages;
    }

    public function getDirectoriesForTheme(string $themeName): array
    {
        /** @var ThemeInterface $theme */
        $theme = $this->themeInstaller->getThemeFromOrganizationThemes($themeName);
        $sourceDir = $theme->getPath();
        $themeDir = $this->themeInstaller->getThemesPath().DIRECTORY_SEPARATOR.basename($sourceDir);

        return [$sourceDir, $themeDir];
    }
}
