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

interface ThemeLogoProviderInterface
{
    public const SETTING_NAME_DEFAULT = 'theme_logo';

    public const SETTING_NAME_SECOND = 'theme_logo_second';

    public const SETTING_NAME_THIRD = 'theme_logo_third';

    /**
     * @param string $settingName
     *
     * @return string
     */
    public function getLogoLink(string $settingName = self::SETTING_NAME_DEFAULT): string;
}
