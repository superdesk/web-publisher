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
     * @var TenantContextInterface
     */
    protected $tenantContext;

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
        RouterInterface $router,
        TenantContextInterface $tenantContext
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->mediaFactory = $mediaFactory;
        $this->filesystem = $filesystem;
        $this->router = $router;
        $this->tenantContext = $tenantContext;
    }

    /**
     * {@inheritdoc}
     */
    public function handleUploadedFile(UploadedFile $uploadedFile, $mediaId)
    {
        $mediaId = $this->handleMediaId($mediaId);
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
        $tenant = $this->tenantContext->getTenant();

        if ($subdomain = $tenant->getSubdomain()) {
            $context = $this->router->getContext();
            $context->setHost($subdomain.'.'.$context->getHost());
        }

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

    /**
     * {@inheritdoc}
     */
    public function handleMediaId($mediaId)
    {
        $mediaId = preg_replace('/\\.[^.\\s]{3,4}$/', '', $mediaId);
        $mediaIdElements = explode('/', $mediaId);
        if (count($mediaIdElements) == 2) {
            return $mediaIdElements[1];
        }

        return $mediaId;
    }

    protected function getMediaBasePath(): string
    {
        $tenant = $this->tenantContext->getTenant();
        $pathElements = ['swp', $tenant->getOrganization()->getCode(), $tenant->getCode(), 'media'];

        return implode('/', $pathElements);
    }
}
