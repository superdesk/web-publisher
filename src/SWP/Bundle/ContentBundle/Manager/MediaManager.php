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
use SWP\Bundle\ContentBundle\Factory\MediaFactoryInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use League\Flysystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;

class MediaManager implements MediaManagerInterface
{
    /**
     * @var MediaFactoryInterface
     */
    protected $mediaFactory;

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
     * MediaManager constructor.
     *
     * @param ArticleMediaRepositoryInterface $mediaRepository
     * @param MediaFactoryInterface           $mediaFactory
     * @param Filesystem                      $filesystem
     * @param RouterInterface                 $router
     * @param TenantContextInterface          $tenantContext
     */
    public function __construct(
        ArticleMediaRepositoryInterface $mediaRepository,
        MediaFactoryInterface $mediaFactory,
        Filesystem $filesystem,
        RouterInterface $router//,
)
    {
        $this->mediaRepository = $mediaRepository;
        $this->mediaFactory = $mediaFactory;
        $this->filesystem = $filesystem;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function handleUploadedFile(UploadedFile $uploadedFile, $mediaId)
    {
        $mediaId = ArticleMedia::handleMediaId($mediaId);
        $this->saveFile($uploadedFile, $mediaId);

        $asset = $this->mediaFactory->createMediaAsset($uploadedFile, $mediaId);
        $this->mediaRepository->add($asset);

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
    public function saveFile(UploadedFile $uploadedFile, $fileName)
    {
        $stream = fopen($uploadedFile->getRealPath(), 'r+');
        $result = $this->filesystem->writeStream($this->getMediaBasePath().'/'.$fileName.'.'.$uploadedFile->guessClientExtension(), $stream);
        fclose($stream);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaPublicUrl(FileInterface $media)
    {
        return $this->getMediaUri($media, RouterInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaUri(FileInterface $media, $type = RouterInterface::ABSOLUTE_PATH)
    {
        return $this->router->generate('swp_media_get', [
            'mediaId' => $media->getAssetId(),
            'extension' => $media->getFileExtension(),
        ], $type);
    }

    protected function getMediaBasePath(): string
    {
        $pathElements = ['swp', 'media'];

        return implode('/', $pathElements);
    }
}
