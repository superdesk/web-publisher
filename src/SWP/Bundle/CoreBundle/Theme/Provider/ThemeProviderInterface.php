<?php

/**
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\CoreBundle\Theme\Provider;

use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;

interface ThemeProviderInterface
{
    /**
     * Gets available themes for current tenant.
     *
     * @return ThemeInterface[]
     */
    public function getCurrentTenantAvailableThemes();
}
