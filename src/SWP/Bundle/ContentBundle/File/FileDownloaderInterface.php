<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileDownloaderInterface
{
    public function download(string $url, string $mediaId, string $mimeType = null): UploadedFile;
}
