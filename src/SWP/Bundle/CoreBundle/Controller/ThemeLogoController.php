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

namespace SWP\Bundle\CoreBundle\Controller;

use League\Flysystem\Filesystem;
use Psr\Cache\CacheItemInterface;
use SWP\Bundle\CoreBundle\Theme\Uploader\ThemeLogoUploaderInterface;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\CacheInterface;

class ThemeLogoController extends Controller {

  private Filesystem $filesystem;
  private ThemeLogoUploaderInterface $themeLogoUploader;
  private CacheInterface $cacheInterface;

  /**
   * @param Filesystem $filesystem
   * @param ThemeLogoUploaderInterface $themeLogoUploader
   * @param CacheInterface $cacheInterface
   */
  public function __construct(Filesystem     $filesystem, ThemeLogoUploaderInterface $themeLogoUploader,
                              CacheInterface $cacheInterface) {
    $this->filesystem = $filesystem;
    $this->themeLogoUploader = $themeLogoUploader;
    $this->cacheInterface = $cacheInterface;
  }

  /**
   * @Route("/theme_logo/{id}", options={"expose"=true}, requirements={"id"=".+"}, methods={"GET"}, name="swp_theme_logo_get")
   */
  public function getLogoAction(string $id) {
    $cacheKey = md5(serialize(['upload', $id]));
    return $this->cacheInterface->get($cacheKey, function (CacheItemInterface $item, &$save) use ($id) {
      $item->expiresAfter(63072000);

      $fileSystem = $this->filesystem;
      $themeLogoUploader = $this->themeLogoUploader;
      $id = $themeLogoUploader->getThemeLogoUploadPath($id);
      $file = $fileSystem->has($id);
      if (!$file) {
        $save = false;
        throw new NotFoundHttpException('File was not found.');
      }

      $path = $fileSystem->get($id)->getPath();
      $response = new Response();
      $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, pathinfo($path, PATHINFO_BASENAME));
      $response->headers->set('Content-Disposition', $disposition);
      $response->headers->set('Content-Type', MimeTypes::getDefault()->guessMimeType($path));
      $response->setPublic();
      $response->setMaxAge(63072000);
      $response->setSharedMaxAge(63072000);
      $response->setContent($fileSystem->read($path));

      return $response;
    });
  }
}
