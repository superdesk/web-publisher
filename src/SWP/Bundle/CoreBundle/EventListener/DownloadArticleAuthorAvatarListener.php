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

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Hoa\Mime\Mime;
use SWP\Bundle\ContentBundle\Model\AuthorMediaInterface;
use SWP\Bundle\CoreBundle\Model\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use SWP\Bundle\ContentBundle\Model\AuthorMedia;
use Doctrine\Common\Collections\ArrayCollection;
use SWP\Bundle\CoreBundle\Model\PackageInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use SWP\Bundle\ContentBundle\Manager\MediaManagerInterface;
use SWP\Component\Common\Exception\UnexpectedTypeException;

final class DownloadArticleAuthorAvatarListener {
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

  public function __construct(EntityManagerInterface $entityManager, $cacheDirectory) {
    $this->entityManager = $entityManager;
    $this->cacheDirectory = $cacheDirectory;
  }

  /**
   * @param MediaManagerInterface $authorMediaManager
   */
  public function setAuthorMediaManager(MediaManagerInterface $authorMediaManager) {
    $this->authorMediaManager = $authorMediaManager;
  }

  /**
   * @param GenericEvent $event
   */
  public function processAuthors(GenericEvent $event): void {
    $package = $event->getSubject();

    if (!$package instanceof PackageInterface) {
      throw new UnexpectedTypeException($package, PackageInterface::class);
    }

    $authors = [];
    /** @var ArticleAuthorInterface $packageAuthor */
    foreach ($package->getAuthors() as $packageAuthor) {
      $authors[] = $this->handle($packageAuthor);
    }
    $package->setAuthors(new ArrayCollection($authors));
  }

  /**
   * @param ArticleAuthorInterface $object
   *
   * @return ArticleAuthorInterface
   */
  private function handle(ArticleAuthorInterface $object): ArticleAuthorInterface {
    if (null !== $object->getAvatarUrl()) {
      $filesystem = new Filesystem();
      $pathParts = \pathinfo($object->getAvatarUrl());
      $assetId = \sha1($pathParts['filename']);
      if (null !== $object->getSlug()) {
        $assetId = $object->getSlug() . '_' . $assetId;
      }
      $existingAvatar = $this->entityManager->getRepository(Image::class)->findBy(['assetId' => $assetId]);
      if (\count($existingAvatar) > 0) {
        $object->setAvatarUrl((string)\reset($existingAvatar));

        return $object;
      }

      try {
        $tempDirectory = $this->cacheDirectory . \DIRECTORY_SEPARATOR . 'downloaded_avatars';
        $tempLocation = $tempDirectory . \DIRECTORY_SEPARATOR . \sha1($assetId . date('his'));
        if (!$filesystem->exists($tempDirectory)) {
          $filesystem->mkdir($tempDirectory);
        }

        $avatarUrl = $object->getAvatarUrl();
        if (strpos($avatarUrl, "http://") === 0 || strpos($avatarUrl, "https://") === 0) {
          self::downloadFile($avatarUrl, $tempLocation);
        } else {
          $file = \file_get_contents($avatarUrl);
          if (false === $file) {
            throw new \Exception('File can\'t be downloaded');
          }
          $filesystem->dumpFile($tempLocation, $file);
        }

        $uploadedFile = new UploadedFile($tempLocation,
            $assetId,
            Mime::getMimeFromExtension($pathParts['extension']),
            \filesize($tempLocation),
            true
        );
      } catch (\Exception $e) {
        return $object;
      }
      /** @var Image $image */
      $image = $this->authorMediaManager->handleUploadedFile($uploadedFile, $assetId);
      $avatar = $this->createAuthorMedia($object, $image);
      $this->entityManager->persist($avatar);
      $this->entityManager->persist($image);

      $object->setAvatar($avatar);
      $object->setAvatarUrl((string)$image);
    }

    return $object;
  }

  private function createAuthorMedia(ArticleAuthorInterface $articleAuthor, Image $image): AuthorMediaInterface {
    return new AuthorMedia('avatar', $articleAuthor, $image);
  }

  private static function downloadFile($url, $location) {
    $handlerStack = HandlerStack::create(new CurlHandler());
    $client = new Client(['handler' => $handlerStack]);
    $client->request('GET', $url, ['sink' => $location]);
  }
}
