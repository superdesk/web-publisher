<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Provider;

use SWP\Bundle\ContentBundle\Doctrine\FileRepositoryInterface;
use SWP\Bundle\ContentBundle\Doctrine\ImageRepositoryInterface;
use SWP\Bundle\ContentBundle\File\FileExtensionCheckerInterface;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\CoreBundle\Util\MimeTypeHelper;

final class FileProvider implements FileProviderInterface
{
    /**
     * @var ImageRepositoryInterface
     */
    private $imageRepository;

    /**
     * @var FileRepositoryInterface
     */
    private $fileRepository;

    /**
     * @var FileExtensionCheckerInterface
     */
    private $fileExtensionChecker;

    public function __construct(
        ImageRepositoryInterface $imageRepository,
        FileRepositoryInterface $fileRepository,
        FileExtensionCheckerInterface $fileExtensionChecker
    ) {
        $this->imageRepository = $imageRepository;
        $this->fileRepository = $fileRepository;
        $this->fileExtensionChecker = $fileExtensionChecker;
    }

    public function getFile(string $id, string $extension): ?FileInterface
    {
        $mimeType = MimeTypeHelper::getByExtension($extension);

        if (empty($mimeType)) {
            return null;
        }

        if ($this->fileExtensionChecker->isImage($mimeType)) {
            return $this->imageRepository->findImageByAssetId($id);
        }

        return $this->fileRepository->findFileByAssetId($id);
    }
}
