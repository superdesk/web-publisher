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

use SWP\Bundle\ContentBundle\Doctrine\ArticleMediaRepositoryInterface;
use SWP\Bundle\ContentBundle\Factory\FileFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Resolver\AssetLocationResolverInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use League\Flysystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;

class MediaManager implements MediaManagerInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var ArticleMediaRepositoryInterface
     */
    protected $mediaRepository;

    /**
     * @var FileFactoryInterface
     */
    protected $fileFactory;

    private $assetLocationResolver;

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

    /**
     * {@inheritdoc}
     */
    public function handleUploadedFile(UploadedFile $uploadedFile, $mediaId)
    {
        $mediaId = ArticleMedia::handleMediaId($mediaId);
        $asset = $this->createMediaAsset($uploadedFile, $mediaId);
        $this->saveFile($uploadedFile, $mediaId);
        $this->mediaRepository->persist($asset);

        return $asset;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(FileInterface $media)
    {
        return $this->filesystem->read($this->getMediaBasePath().'/'.$media->getAssetId().'.'.$media->getFileExtension());
    }

    /**
     * {@inheritdoc}
     */
    public function saveFile(UploadedFile $uploadedFile, $fileName): bool
    {
        $filePath = $this->getMediaBasePath().'/'.$fileName.'.'.$this->guessExtension($uploadedFile);

        if ($this->filesystem->has($filePath)) {
            return true;
        }

        $stream = fopen($uploadedFile->getRealPath(), 'r+');
        $result = $this->filesystem->writeStream($filePath, $stream);
        fclose($stream);

        return $result;
    }

    public function getMediaPublicUrl(FileInterface $media): string
    {
        return $this->getMediaUri($media, RouterInterface::ABSOLUTE_URL);
    }

    public function getMediaUri(FileInterface $media, $type = RouterInterface::ABSOLUTE_PATH): string
    {
        $uri = '/'.$this->assetLocationResolver->getAssetUrl($media);
        if (RouterInterface::ABSOLUTE_URL === $type) {
            $requestContext = $this->router->getContext();
            $uri = $requestContext->getScheme().'://'.$requestContext->getHost().$uri;
        }

        return $uri;
    }

    public function createMediaAsset(UploadedFile $uploadedFile, string $assetId): FileInterface
    {
        return $this->fileFactory->createWith($assetId, $this->guessExtension($uploadedFile));
    }

    public function getMediaBasePath(): string
    {
        return $this->assetLocationResolver->getMediaBasePath();
    }

    private function guessExtension(UploadedFile $uploadedFile): string
    {
        $extension = $uploadedFile->guessExtension();
        if ('mpga' === $extension && 'mp3' === $uploadedFile->getClientOriginalExtension()) {
            $extension = 'mp3';
        }

        return $extension;
    }
}
