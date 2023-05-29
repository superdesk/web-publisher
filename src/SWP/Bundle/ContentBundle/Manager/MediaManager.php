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

use League\Flysystem\FileExistsException;
use League\Flysystem\Filesystem;
use SWP\Bundle\ContentBundle\Doctrine\ArticleMediaRepositoryInterface;
use SWP\Bundle\ContentBundle\Factory\FileFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Model\ImageInterface;
use SWP\Bundle\ContentBundle\Resolver\AssetLocationResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;

class MediaManager implements MediaManagerInterface
{
    protected $filesystem;

    protected $router;

    protected $mediaRepository;

    protected $fileFactory;

    protected $assetLocationResolver;

    public function __construct(
        ArticleMediaRepositoryInterface $mediaRepository,
        Filesystem $filesystem,
        Router $router,
        FileFactoryInterface $fileFactory,
        AssetLocationResolverInterface $assetLocationResolver
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->filesystem = $filesystem;
        $this->router = $router;
        $this->fileFactory = $fileFactory;
        $this->assetLocationResolver = $assetLocationResolver;
    }

    public function handleUploadedFile(UploadedFile $uploadedFile, $mediaId): FileInterface
    {
        $mediaId = ArticleMedia::handleMediaId($mediaId);
        $asset = $this->fileFactory->createWith($mediaId, $this->guessExtension($uploadedFile));
        if ($asset instanceof ImageInterface) {
            [$width, $height] = getimagesize($uploadedFile->getRealPath());
            $asset->setWidth($width);
            $asset->setHeight($height);
            $size = filesize($uploadedFile->getRealPath());
            if ($size) {
                $asset->setLength(round($size / 1024));
            }
        }

        $this->mediaRepository->persist($asset);
        $this->saveFile($uploadedFile, $mediaId);

        return $asset;
    }

    public function getFile(FileInterface $asset)
    {
        return $this->filesystem->read($this->assetLocationResolver->getMediaBasePath().'/'.$asset->getAssetId().'.'.$asset->getFileExtension());
    }

    public function getMediaPublicUrl(FileInterface $media): string
    {
        return $this->getMediaUri($media, RouterInterface::ABSOLUTE_URL);
    }

    public function getMediaUri(FileInterface $media, $type = RouterInterface::ABSOLUTE_PATH): string
    {
        $uri = $this->assetLocationResolver->getAssetUrl($media);
        if (0 === strpos($uri, 'http')) {
            return $uri;
        }

        $uri = '/'.$uri;
        if (RouterInterface::ABSOLUTE_URL === $type) {
            $requestContext = $this->router->getContext();
            $uri = $requestContext->getScheme().'://'.$requestContext->getHost().$uri;
        }

        return $uri;
    }

    public function saveFile(UploadedFile $uploadedFile, $fileName): void
    {
        $filePath = $this->assetLocationResolver->getMediaBasePath().'/'.$fileName.'.'.$this->guessExtension($uploadedFile);
        if ($this->filesystem->has($filePath)) {
            return;
        }

        try {
            $stream = fopen($uploadedFile->getRealPath(), 'rb+');
            $this->filesystem->writeStream($filePath, $stream);
            if (is_resource($stream) && get_resource_type($stream) === 'stream') {
                fclose($stream);
            }
        } catch (\Throwable $e) {
            /*
            Handle case when multiple instances work with this same storage
            As content push is async then there can be a situation when other instance
            will save that file in between of file exist check and actual saving
            */
            return;
        }
    }

    private function guessExtension(UploadedFile $uploadedFile): string
    {
        $extension = $uploadedFile->guessExtension();
        $clientOriginalExtension = $uploadedFile->getClientOriginalExtension();
        if ('mpga' === $extension && 'mp3' === $clientOriginalExtension) {
            $extension = 'mp3';
        }

        return null !== $extension ? $extension : $clientOriginalExtension;
    }
}
