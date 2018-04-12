<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Installer;

use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;

/**
 * Interface ThemeUploaderInterface.
 */
interface ThemeInstallerInterface
{
    /**
     * @param null $sourceDir
     * @param null $themeDir
     *
     * @return null|ThemeInterface
     */
    public function install($sourceDir = null, $themeDir = null): void;

    /**
     * @param string $themeName
     *
     * @return ThemeInterface
     */
    public function getThemeFromOrganizationThemes(string $themeName): ThemeInterface;

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getThemesPath();
}
