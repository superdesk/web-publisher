<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Context;

use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;
use SWP\Bundle\SettingsBundle\Context\ScopeContext as BaseScopeContext;
use SWP\Bundle\SettingsBundle\Model\SettingsOwnerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;

class ScopeContext extends BaseScopeContext implements ScopeContextInterface
{
    const NEW_SCOPES = [
        ScopeContextInterface::SCOPE_ORGANIZATION,
        ScopeContextInterface::SCOPE_TENANT,
        ScopeContextInterface::SCOPE_THEME,
    ];

    private $tenantContent;

    private $themeContext;

    public function __construct(TenantContextInterface $tenantContext, ThemeContextInterface $themeContext)
    {
        $this->tenantContent = $tenantContext;
        $this->themeContext = $themeContext;
    }

    public function getScopes(): array
    {
        return array_merge(parent::getScopes(), self::NEW_SCOPES);
    }

    public function getScopesOwners(): array
    {
        foreach ($this->getScopes() as $scope) {
            $this->lazyLoad($scope);
        }

        return parent::getScopesOwners();
    }

    public function getScopeOwner(string $scope)
    {
        $this->lazyLoad($scope);

        return parent::getScopeOwner($scope);
    }

    private function lazyLoad(string $scope)
    {
        if (in_array($scope, self::NEW_SCOPES)) {
            $tenant = $this->tenantContent->getTenant();
            if ($tenant instanceof SettingsOwnerInterface) {
                if (ScopeContextInterface::SCOPE_TENANT === $scope) {
                    $this->setScopeOwner(ScopeContextInterface::SCOPE_TENANT, $tenant);
                } elseif (ScopeContextInterface::SCOPE_ORGANIZATION === $scope) {
                    $this->setScopeOwner(ScopeContextInterface::SCOPE_ORGANIZATION, $tenant->getOrganization());
                } elseif (ScopeContextInterface::SCOPE_THEME === $scope) {
                    if ($this->themeContext->getTheme() instanceof ThemeInterface) {
                        $this->setScopeOwner(ScopeContextInterface::SCOPE_THEME, $tenant);
                    }
                }
            }
        }
    }
}
