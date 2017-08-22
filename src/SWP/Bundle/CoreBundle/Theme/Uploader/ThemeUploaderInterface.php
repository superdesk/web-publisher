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

namespace SWP\Bundle\CoreBundle\Theme\Uploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface ThemeUploaderInterface.
 */
interface ThemeUploaderInterface
{
    const AVAILABLE_THEMES_PATH = 'web'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'available_themes'.DIRECTORY_SEPARATOR.'%s';

    /**
     * @param UploadedFile $file
     *
     * @return bool True when successfully uploaded, false otherwise
     */
    public function upload(UploadedFile $file);

    /**
     * @return string
     */
    public function getAvailableThemesPath();
}
