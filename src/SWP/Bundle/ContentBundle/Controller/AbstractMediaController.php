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

use SWP\Bundle\ContentBundle\File\FileExtensionChecker;
use SWP\Bundle\ContentBundle\File\FileExtensionCheckerInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Bundle\ContentBundle\Model\ArticleMedia;
use SWP\Bundle\ContentBundle\Provider\FileProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Hoa\Mime\Mime;

abstract class AbstractMediaController extends Controller
{
    public function getMedia(string $mediaId, string $extension): Response
    {
        $cacheProvider = $this->get('doctrine_cache.providers.main_cache');
        $cacheKey = md5(serialize(['media_file', $mediaId]));
        if (!$cacheProvider->contains($cacheKey)) {
            $fileProvider = $this->container->get(FileProvider::class);
            $media = $fileProvider->getFile(ArticleMedia::handleMediaId($mediaId), $extension);
            $cacheProvider->save($cacheKey, $media, 63072000);
        } else {
            $media = $cacheProvider->fetch($cacheKey);
        }

        if (null === $media) {
            throw new NotFoundHttpException('Media was not found.');
        }

        $response = new Response();
        /** @var FileExtensionCheckerInterface $fileExtensionChecker */
        $fileExtensionChecker = $this->container->get(FileExtensionChecker::class);
        $mimeType = Mime::getMimeFromExtension($extension);

        if (!$fileExtensionChecker->isAttachment($mimeType)) {
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, str_replace('/', '_', $mediaId.'.'.$media->getFileExtension()));
        } else {
            $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, str_replace('/', '_', $mediaId.'.'.$media->getFileExtension()));
        }

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', Mime::getMimeFromExtension($media->getFileExtension()));

        $response->setPublic();
        $response->setMaxAge(63072000);
        $response->setSharedMaxAge(63072000);
        $response->setLastModified($media->getUpdatedAt() ?: $media->getCreatedAt());

        $mediaManager = $this->getMediaManager();

        $response->setContent($mediaManager->getFile($media));

        return $response;
    }

    abstract public function getMediaManager(): MediaManagerInterface;
}
