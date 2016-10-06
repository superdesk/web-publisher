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
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use League\Flysystem\Filesystem;
use Doctrine\ODM\PHPCR\DocumentManager;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\File;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Image;
use Symfony\Component\Routing\RouterInterface;

class MediaManager implements MediaManagerInterface
{
    /**
     * @var TenantAwarePathBuilder
     */
    protected $pathBuilder;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var DocumentManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $mediaBasepath;

    /**
     * MediaManager constructor.
     *
     * @param TenantAwarePathBuilder $pathBuilder
     * @param Filesystem             $filesystem
     * @param DocumentManager        $objectManager
     * @param RouterInterface        $router
     * @param TenantContextInterface $tenantContext
     * @param string                 $mediaBasepath
     */
    public function __construct(
        TenantAwarePathBuilder $pathBuilder,
        Filesystem $filesystem,
        DocumentManager $objectManager,
        RouterInterface $router,
        TenantContextInterface $tenantContext,
        $mediaBasepath
    ) {
        $this->pathBuilder = $pathBuilder;
        $this->filesystem = $filesystem;
        $this->objectManager = $objectManager;
        $this->mediaBasepath = $mediaBasepath;
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

        $media = $this->getProperObject($uploadedFile);
        $media->setParentDocument($this->objectManager->find(null, $this->pathBuilder->build($this->mediaBasepath)));
        $media->setId($mediaId);
        $media->setFileExtension($uploadedFile->guessClientExtension());
        $this->objectManager->persist($media);
        $this->objectManager->flush();

        return $media;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(FileInterface $media)
    {
        $mediaBasePath = $this->pathBuilder->build($this->mediaBasepath);

        return $this->filesystem->read($mediaBasePath.'/'.$media->getId().'.'.$media->getFileExtension());
    }

    /**
     * {@inheritdoc}
     */
    public function saveFile(UploadedFile $uploadedFile, $fileName)
    {
        $stream = fopen($uploadedFile->getRealPath(), 'r+');
        $result = $this->filesystem->writeStream($this->pathBuilder->build($this->mediaBasepath).'/'.$fileName.'.'.$uploadedFile->guessClientExtension(), $stream);
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
            'mediaId' => $media->getId(),
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

    protected function getProperObject(UploadedFile $uploadedFile)
    {
        if (in_array(exif_imagetype($uploadedFile->getRealPath()), [
            IMAGETYPE_GIF,
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_BMP,
        ])) {
            return new Image();
        }

        return new File();
    }
}
