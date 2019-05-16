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
use SWP\Bundle\ContentBundle\Model\File;

interface MediaManagerInterface
{
    /**
     * Process UploadedFile and return his database representation.
     *
     * @param UploadedFile $uploadedFile
     * @param string       $mediaId
     *
     * @return File
     */
    public function handleUploadedFile(UploadedFile $uploadedFile, $mediaId);

    /**
     * @param FileInterface $media
     *
     * @return string|false The file contents or false on failure
     */
    public function getFile(FileInterface $media);

    /**
     * Save file to files storage.
     *
     * @param UploadedFile $uploadedFile
     * @param string       $fileName
     *
     * @return bool True on success, false on failure
     */
    public function saveFile(UploadedFile $uploadedFile, $fileName);

    public function getMediaPublicUrl(FileInterface $media): string;

    public function getMediaUri(FileInterface $media): string;

    public function createMediaAsset(UploadedFile $uploadedFile, string $assetId): FileInterface;
}
