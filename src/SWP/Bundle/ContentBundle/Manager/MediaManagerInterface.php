<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Manager;

use SWP\Bundle\ContentBundle\Model\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MediaManagerInterface
{
    public function handleUploadedFile(UploadedFile $uploadedFile, $mediaId): FileInterface;

    public function getFile(FileInterface $media);

    public function getMediaPublicUrl(FileInterface $media): string;

    public function getMediaUri(FileInterface $media): string;

    public function saveFile(UploadedFile $uploadedFile, $fileName): void;
}
