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
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
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
     * @var TenantContextInterface
     */
    private $tenantContext;

    /**
     * ThemeLogoProvider constructor.
     *
     * @param SettingsManagerInterface $settingsManager
     * @param RouterInterface          $router
     * @param TenantContextInterface   $tenantContext
     */
    public function __construct(
        SettingsManagerInterface $settingsManager,
        RouterInterface $router,
        TenantContextInterface $tenantContext
    ) {
        $this->settingsManager = $settingsManager;
        $this->router = $router;
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogoLink(): string
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->tenantContext->getTenant();
        $setting = $this->settingsManager->get(
            'theme_logo',
            ScopeContextInterface::SCOPE_THEME,
            $tenant
        );

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
