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

namespace SWP\Bundle\ContentBundle\Factory;

use SWP\Bundle\ContentBundle\File\FileExtensionCheckerInterface;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\Mime\MimeTypes;

class FileFactory implements FileFactoryInterface
{
    /**
     * @var FileExtensionCheckerInterface
     */
    private $fileExtensionChecker;

    /**
     * @var FactoryInterface
     */
    private $imageFactory;

    /**
     * @var FactoryInterface
     */
    private $fileFactory;

    public function __construct(
        FileExtensionCheckerInterface $fileExtensionChecker,
        FactoryInterface $imageFactory,
        FactoryInterface $fileFactory
    ) {
        $this->fileExtensionChecker = $fileExtensionChecker;
        $this->imageFactory = $imageFactory;
        $this->fileFactory = $fileFactory;
    }

    public function createWith(string $assetId, string $extension): FileInterface
    {
        $mimeType = MimeTypes::getDefault()->getMimeTypes($extension)[0] ?? null;

        if ($this->fileExtensionChecker->isImage($mimeType)) {
            /** @var ImageInterface $image */
            $image = $this->imageFactory->create();
            $image->setAssetId($assetId);
            $image->setFileExtension($extension);

            return $image;
        }

        /** @var FileInterface $file */
        $file = $this->fileFactory->create();
        $file->setAssetId($assetId);
        $file->setFileExtension($extension);

        return $file;
    }

    public function createFile(): FileInterface
    {
        return $this->fileFactory->create();
    }
}
