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

use Hoa\Mime\Mime;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ThemeLogoController extends Controller
{
    /**
     * @Route("/theme_logo/{id}", options={"expose"=true}, requirements={"id"=".+"}, methods={"GET"}, name="swp_theme_logo_get")
     */
    public function getLogoAction(string $id)
    {
        $cacheProvider = $this->get('doctrine.system_cache_pool');
        $cacheKey = md5(serialize(['upload', $id]));
        if ($cacheProvider->contains($cacheKey)) {
            return $cacheProvider->fetch($cacheKey);
        }

        $fileSystem = $this->get('swp_filesystem');
        $themeLogoUploader = $this->get('swp_core.uploader.theme_logo');
        $id = $themeLogoUploader->getThemeLogoUploadPath($id);

        $file = $fileSystem->has($id);

        if (!$file) {
            throw new NotFoundHttpException('File was not found.');
        }

        $path = $fileSystem->get($id)->getPath();

        $response = new Response();
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, pathinfo($path, PATHINFO_BASENAME));

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', Mime::getMimeFromExtension($path));

        $response->setPublic();
        $response->setMaxAge(63072000);
        $response->setSharedMaxAge(63072000);

        $response->setContent($fileSystem->read($path));
        $cacheProvider->save($cacheKey, $response, 63072000);

        return $response;
    }
}
