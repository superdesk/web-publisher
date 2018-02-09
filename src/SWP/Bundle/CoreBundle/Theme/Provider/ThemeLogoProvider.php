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

namespace SWP\Bundle\CoreBundle\Theme\Provider;

use SWP\Bundle\CoreBundle\Context\ScopeContextInterface;
use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ThemeLogoProvider implements ThemeLogoProviderInterface
{
    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * ThemeLogoProvider constructor.
     *
     * @param SettingsManagerInterface $settingsManager
     * @param RouterInterface          $router
     */
    public function __construct(SettingsManagerInterface $settingsManager, RouterInterface $router)
    {
        $this->settingsManager = $settingsManager;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogoLink(ThemeInterface $theme): string
    {
        $setting = $this->settingsManager->get('theme_logo', ScopeContextInterface::SCOPE_THEME, $theme);

        if ('' === $setting) {
            return $setting;
        }

        return $this->router->generate(
            'swp_theme_logo_get',
            [
                'id' => $setting,
            ],
            UrlGeneratorInterface::RELATIVE_PATH
        );
    }
}
