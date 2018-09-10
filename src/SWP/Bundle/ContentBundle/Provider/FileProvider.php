<?php

declare(strict_types=1);

namespace SWP\Bundle\ContentBundle\Provider;

use Hoa\Mime\Mime;
use SWP\Bundle\ContentBundle\Doctrine\FileRepositoryInterface;
use SWP\Bundle\ContentBundle\Doctrine\ImageRepositoryInterface;
use SWP\Bundle\ContentBundle\File\FileExtensionCheckerInterface;
use SWP\Bundle\ContentBundle\Model\FileInterface;

final class FileProvider implements FileProviderInterface
{
    private $imageRepository;

    private $fileRepository;

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
        $mimeType = Mime::getMimeFromExtension($extension);

        if (null === $mimeType) {
            return null;
        }

        if ($this->fileExtensionChecker->isVideo($mimeType) || $this->fileExtensionChecker->isAudio($mimeType)) {
            return $this->fileRepository->findOneBy(['assetId' => $id]);
        }

        if ($this->fileExtensionChecker->isImage($mimeType)) {
            return $this->imageRepository->findImageByAssetId($id);
        }

        return null;
    }
}
