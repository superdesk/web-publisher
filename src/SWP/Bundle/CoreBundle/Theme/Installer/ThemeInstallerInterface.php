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

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * Interface ThemeUploaderInterface.
 */
interface ThemeInstallerInterface
{
    /**
     * @param string $themeName
     *
     * @return ThemeInterface|bool Theme when successful, false otherwise
     */
    public function install(string $themeName);

    /**
     * @return string
     */
    public function getThemesPath();
}
