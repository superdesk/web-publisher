<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Twig;

use SWP\Bundle\CoreBundle\Context\ScopeContextInterface;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeLogoProviderInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ThemeExtension extends AbstractExtension
{
    /**
     * @var ThemeLogoProviderInterface
     */
    private $themeLogoProvider;

    /**
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var string
     */
    private $env;

    /**
     * ThemeExtension constructor.
     *
     * @param ThemeLogoProviderInterface $themeLogoProvider
     * @param TenantContextInterface     $tenantContext
     * @param SettingsManagerInterface   $settingsManager
     * @param string                     $env
     */
    public function __construct(
        ThemeLogoProviderInterface $themeLogoProvider,
        TenantContextInterface $tenantContext,
        SettingsManagerInterface $settingsManager,
        string $env
    ) {
        $this->themeLogoProvider = $themeLogoProvider;
        $this->tenantContext = $tenantContext;
        $this->settingsManager = $settingsManager;
        $this->env = $env;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('themeLogo', [$this, 'getThemeLogoPath']),
            new TwigFunction('themeSetting', [$this, 'getThemeSetting']),
        ];
    }

    /**
     * @param string $fallBackPath
     *
     * @return string
     */
    public function getThemeLogoPath(string $fallBackPath): string
    {
        $link = $this->themeLogoProvider->getLogoLink();

        if ('' === $link) {
            return $fallBackPath;
        }

        return $link;
    }

    /**
     * @param string $setting
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getThemeSetting(string $setting): string
    {
        try {
            return $this->getSetting($setting);
        } catch (\Exception $e) {
            if ('prod' === $this->env) {
                return '';
            }

            throw $e;
        }
    }

    private function getSetting(string $setting): string
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->tenantContext->getTenant();

        return (string) $this->settingsManager->get(
            $setting,
            ScopeContextInterface::SCOPE_THEME,
            $tenant
        );
    }
}
