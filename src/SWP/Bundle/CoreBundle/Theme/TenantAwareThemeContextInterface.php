<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme;

use SWP\Component\Common\Model\ThemeAwareTenantInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;

/**
 * Interface TenantAwareThemeContextInterface.
 */
interface TenantAwareThemeContextInterface extends ThemeContextInterface
{
    /**
     * @param ThemeAwareTenantInterface $tenant
     * @param string|null               $themeName
     *
     * @return null|string
     */
    public function resolveThemeName(ThemeAwareTenantInterface $tenant, string $themeName = null): ?string;
}
