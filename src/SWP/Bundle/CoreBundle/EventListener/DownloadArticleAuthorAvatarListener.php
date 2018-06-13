<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\EventListener;

use Hoa\Mime\Mime;
use SWP\Bundle\CoreBundle\Model\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Collections\ArrayCollection;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;

final class DownloadArticleAuthorAvatarListener
{
    /**
     * @var MediaManagerInterface
     */
    private $authorMediaManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $cacheDirectory;

    public function __construct(EntityManagerInterface $entityManager, $cacheDirectory)
    {
        $this->entityManager = $entityManager;
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * @param MediaManagerInterface $authorMediaManager
     */
    public function setAuthorMediaManager(MediaManagerInterface $authorMediaManager)
    {
        $this->authorMediaManager = $authorMediaManager;
    }

    /**
     * @param GenericEvent $event
     */
    public function processAuthors(GenericEvent $event): void
    {
        $package = $event->getSubject();

        if (!$package instanceof PackageInterface) {
            throw new UnexpectedTypeException($package, PackageInterface::class);
        }

        $authors = [];
        /** @var ArticleAuthorInterface $packageAuthor */
        foreach ($package->getAuthors()->toArray() as $packageAuthor) {
            $authors[] = $this->handle($packageAuthor);
        }

        $package->setAuthors(new ArrayCollection($authors));
    }

    /**
     * @param ArticleAuthorInterface $object
     *
     * @return ArticleAuthorInterface
     */
    private function handle(ArticleAuthorInterface $object): ArticleAuthorInterface
    {
        if (null !== $object->getAvatarUrl()) {
            $filesystem = new Filesystem();
            $pathParts = \pathinfo($object->getAvatarUrl());
            $existingAvatar = $this->entityManager->getRepository(Image::class)->findBy(['assetId' => $pathParts['filename']]);
            if (\count($existingAvatar) > 0) {
                $object->setAvatarUrl($this->authorMediaManager->getMediaPublicUrl(\reset($existingAvatar)));

                return $object;
            }

            $assetId = $object->getSlug().'_'.$pathParts['filename'];

            try {
                $file = \file_get_contents($object->getAvatarUrl());
                $tempDirectory = $this->cacheDirectory.\DIRECTORY_SEPARATOR.'downloaded_avatars';
                $tempLocation = $tempDirectory.\DIRECTORY_SEPARATOR.\sha1($assetId.date('his'));
                if (!$filesystem->exists($tempDirectory)) {
                    $filesystem->mkdir($tempDirectory);
                }
                \file_put_contents($tempLocation, $file);                
                $uploadedFile = new UploadedFile($tempLocation, $assetId, Mime::getMimeFromExtension($pathParts['extension']), \strlen($file), null, true);
            } catch (\Exception $e) {
                return $object;
            }
            /** @var Image $image */
            $image = $this->authorMediaManager->handleUploadedFile($uploadedFile, $assetId);
            $this->entityManager->persist($image);
            $this->entityManager->flush();
            $object->setAvatarUrl($this->authorMediaManager->getMediaPublicUrl($image));
        }

        return $object;
    }
}
