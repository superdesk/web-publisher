<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Controller;

use Hoa\File\Read;
use Hoa\Mime\Mime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StaticThemeAssetsController extends Controller
{
    /**
     * Directory with assets inside theme.
     */
    const ASSETS_DIRECTORY = 'public';

    /**
     * @Route("/{fileName}.{fileExtension}", name="static_theme_assets_root", requirements={
     *     "fileName": "sw|manifest"
     * })
     * @Method("GET")
     */
    public function rootAction($fileName, $fileExtension)
    {
        $themes = $this->get('sylius.theme.hierarchy_provider')->getThemeHierarchy(
            $this->get('sylius.context.theme')->getTheme()
        );
        foreach ($themes as $theme) {
            $filePath = $theme->getPath().'/'.self::ASSETS_DIRECTORY.'/'.$fileName.'.'.$fileExtension;
            if (null !== $response = $this->handleFileLoading($filePath)) {
                return $response;
            }
        }

        throw new NotFoundHttpException('Page was not found.');
    }

    /**
     * @Route("/public/{filePath}", name="static_theme_assets_public", requirements={"filePath"=".+"})
     * @Method("GET")
     */
    public function publicAction($filePath)
    {
        $themes = $this->get('sylius.theme.hierarchy_provider')->getThemeHierarchy(
            $this->get('sylius.context.theme')->getTheme()
        );
        foreach ($themes as $theme) {
            $themeFilePath = $theme->getPath().'/'.self::ASSETS_DIRECTORY.'/'.$filePath;
            if (null !== $response = $this->handleFileLoading($themeFilePath, basename($filePath))) {
                return $response;
            }
        }

        throw new NotFoundHttpException('File was not found.', null, 404);
    }

    /**
     * @param $filePath
     *
     * @return Response
     */
    private function handleFileLoading($filePath)
    {
        if (file_exists($filePath)) {
            $response = new Response(file_get_contents($filePath));
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                basename($filePath)
            );
            $response->headers->set('Content-Disposition', $disposition);
            $type = new Mime(new Read($filePath));
            $mime = str_replace('/x-', '/', Mime::getMimeFromExtension($type->getExtension()));
            $response->headers->set('Content-Type', $mime);
            $response->setStatusCode(Response::HTTP_OK);

            return $response;
        }
    }
}
