<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Service;

interface ThemeServiceInterface
{
    /**
     * @param string $sourceDir
     * @param string $themeDir
     *
     * @return array|\Exception
     */
    public function installAndProcessGeneratedData(string $sourceDir, string $themeDir);

    /**
     * @param string $themeName
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getDirectoriesForTheme(string $themeName): array;
}
