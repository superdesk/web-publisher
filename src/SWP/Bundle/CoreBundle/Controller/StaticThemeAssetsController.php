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
     * @Route("/{fileName}.{fileExtension}", name="static_theme_assets_root")
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
     * @Route("/themes/{type}/{themeName}/screenshots/{fileName}", name="static_theme_screenshots", requirements={
     *     "type": "organization|tenant"
     * })
     * @Method("GET")
     */
    public function screenshotsAction(string $type, string $themeName, $fileName)
    {
        if ('organization' === $type) {
            $theme = $this->loadOrganizationTheme(str_replace('__', '/', $themeName));
        } elseif ('tenant' === $type) {
            $theme = $this->loadTenantTheme(str_replace('__', '/', $themeName));
        }

        $filePath = $theme->getPath().'/screenshots/'.$fileName;
        if (null !== $response = $this->handleFileLoading($filePath)) {
            return $response;
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
            $response->setPublic();
            $response->setMaxAge(3600);
            $response->setSharedMaxAge(7200);

            return $response;
        }
    }

    /**
     * @param string $themeName
     *
     * @return mixed
     */
    private function loadOrganizationTheme(string $themeName)
    {
        $loadedThemes = $this->container->get('swp_core.loader.organization.theme')->load();

        return $this->filterThemes($loadedThemes, $themeName);
    }

    /**
     * @param string $themeName
     *
     * @return mixed
     */
    private function loadTenantTheme(string $themeName)
    {
        $loadedThemes = $this->container->get('sylius.repository.theme')->findAll();

        return $this->filterThemes($loadedThemes, $themeName);
    }

    /**
     * @param array  $loadedThemes
     * @param string $themeName
     *
     * @return mixed
     */
    private function filterThemes($loadedThemes, string $themeName)
    {
        $themes = array_filter(
            $loadedThemes,
            function ($element) use (&$themeName) {
                return $element->getName() === $themeName;
            }
        );

        if (count($themes) === 0) {
            throw new NotFoundHttpException(sprintf('Theme with name "%s" was not found in organization themes.', $themeName));
        }

        return reset($themes);
    }
}
