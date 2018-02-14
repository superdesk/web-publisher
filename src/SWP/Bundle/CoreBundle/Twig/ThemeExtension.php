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
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeLogoProviderInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ThemeExtension extends AbstractExtension
{
    /**
     * @var ThemeLogoProviderInterface
     */
    private $themeLogoProvider;

    /**
     * @var ThemeContextInterface
     */
    private $themeContext;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * ThemeExtension constructor.
     *
     * @param ThemeLogoProviderInterface $themeLogoProvider
     * @param ThemeContextInterface      $themeContext
     * @param SettingsManagerInterface   $settingsManager
     */
    public function __construct(
        ThemeLogoProviderInterface $themeLogoProvider,
        ThemeContextInterface $themeContext,
        SettingsManagerInterface $settingsManager
    ) {
        $this->themeLogoProvider = $themeLogoProvider;
        $this->themeContext = $themeContext;
        $this->settingsManager = $settingsManager;
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
        $link = $this->themeLogoProvider->getLogoLink($this->themeContext->getTheme());

        if ('' === $link) {
            return $fallBackPath;
        }

        return $link;
    }

    /**
     * @param string $setting
     *
     * @return string
     */
    public function getThemeSetting(string $setting): string
    {
        return (string) $this->settingsManager->get(
            $setting,
            ScopeContextInterface::SCOPE_THEME,
            $this->themeContext->getTheme()
        );
    }
}
