<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2019 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2019 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Controller;

use SWP\Bundle\ContentBundle\File\FileExtensionCheckerInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Provider\FileProviderInterface;
use SWP\Bundle\CoreBundle\Util\MimeTypeHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\CacheInterface;

abstract class AbstractMediaController extends AbstractController
{
    protected $mediaManager;

    protected $cacheProvider;

    protected $fileProvider;

    protected $fileExtensionChecker;

    public function __construct(
        MediaManagerInterface $mediaManager,
        CacheInterface $cacheProvider,
        FileProviderInterface $fileProvider,
        FileExtensionCheckerInterface $fileExtensionChecker
    ) {
        $this->mediaManager = $mediaManager;
        $this->cacheProvider = $cacheProvider;
        $this->fileProvider = $fileProvider;
        $this->fileExtensionChecker = $fileExtensionChecker;
    }

    public function getMedia(string $mediaId, string $extension): Response
    {
        $cacheKey = md5(serialize(['media_file', $mediaId]));
        $media = $this->cacheProvider->get($cacheKey, function () use ($mediaId, $extension) {
            return $this->fileProvider->getFile(ArticleMedia::handleMediaId($mediaId), $extension);
        });

        if (null === $media) {
            throw new NotFoundHttpException('Media was not found.');
        }

        $response = new Response();
        $mimeType = MimeTypeHelper::getByExtension($extension);

        if (!$this->fileExtensionChecker->isAttachment($mimeType)) {
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, str_replace('/', '_', $mediaId.'.'.$media->getFileExtension()));
        } else {
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, str_replace('/', '_', $mediaId.'.'.$media->getFileExtension()));
        }

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', MimeTypeHelper::getByExtension($media->getFileExtension()));

        $response->setPublic();
        $response->setMaxAge(63072000);
        $response->setSharedMaxAge(63072000);
        $response->setLastModified($media->getUpdatedAt() ?: $media->getCreatedAt());
        $response->setContent($this->mediaManager->getFile($media));

        return $response;
    }
}
