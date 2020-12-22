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
    public function installAndProcessGeneratedData(string $sourceDir, string $themeDir, bool $processOptionalData = false, bool $activate = false);

    public function getDirectoriesForTheme(string $themeName): array;
}
