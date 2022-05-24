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
use Sylius\Bundle\ThemeBundle\HierarchyProvider\ThemeHierarchyProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class StaticThemeAssetsController extends Controller
{
    /**
     * Directory with assets inside theme.
     */
    const ASSETS_DIRECTORY = 'public';

    /**
     * @Route("/{fileName}.{fileExtension}", methods={"GET"}, name="static_theme_assets_root", requirements={"fileName": "sw|manifest|favicon|ads|OneSignalSDKWorker|OneSignalSDKUpdaterWorker|amp-web-push-helper-frame|amp-web-push-permission-dialog"})
     * @Route("/public-{fileName}.{fileExtension}", methods={"GET"}, name="static_theme_assets_root_public", requirements={"fileName"=".+"})
     * @Route("/public/{fileName}.{fileExtension}", methods={"GET"}, name="static_theme_assets_public", requirements={"fileName"=".+"})
     */
    public function rootAction($fileName, $fileExtension, ThemeHierarchyProviderInterface $themeHierarchyProvider)
    {
        $themes = $themeHierarchyProvider->getThemeHierarchy(
            $this->get('swp_core.theme.context.tenant_aware')->getTheme()
        );

        $fileName = (null === $fileExtension) ? basename($fileName) : $fileName.'.'.$fileExtension;
        foreach ($themes as $theme) {
            $filePath = $theme->getPath().'/'.self::ASSETS_DIRECTORY.'/'.$fileName;
            if (null !== $response = $this->handleFileLoading($filePath)) {
                return $response;
            }
        }

        throw new NotFoundHttpException('File was not found.');
    }

    /**
     * @Route("/themes/{type}/{themeName}/screenshots/{fileName}", methods={"GET"}, name="static_theme_screenshots", requirements={
     *     "type": "organization|tenant"
     * })
     */
    public function screenshotsAction(string $type, string $themeName, $fileName)
    {
        if ('organization' === $type) {
            $theme = $this->loadOrganizationTheme(str_replace('__', '/', $themeName));
        } elseif ('tenant' === $type) {
            $theme = $this->loadTenantTheme(str_replace('__', '/', $themeName));
        } else {
            throw new NotFoundHttpException('File was not found.');
        }

        $filePath = $theme->getPath().'/screenshots/'.$fileName;
        if (null !== $response = $this->handleFileLoading($filePath)) {
            return $response;
        }

        throw new NotFoundHttpException('File was not found.');
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

            try {
                $type = new Mime(new Read($filePath));
                $mime = str_replace('/x-', '/', Mime::getMimeFromExtension($type->getExtension()));
            } catch (\Exception $e) {
                $mime = 'text/plain';
            }

            $response->headers->set('Content-Type', $mime);
            $response->setStatusCode(Response::HTTP_OK);
            $response->setPublic();
            $response->setMaxAge(3600);
            $response->setSharedMaxAge(7200);

            return $response;
        }
    }

    /**
     * @return mixed
     */
    private function loadOrganizationTheme(string $themeName)
    {
        $loadedThemes = $this->get('swp_core.loader.organization.theme')->load();

        return $this->filterThemes($loadedThemes, $themeName);
    }

    /**
     * @return mixed
     */
    private function loadTenantTheme(string $themeName)
    {
        $loadedThemes = $this->get('sylius.repository.theme')->findAll();

        return $this->filterThemes($loadedThemes, $themeName);
    }

    /**
     * @param array $loadedThemes
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

        if (0 === count($themes)) {
            throw new NotFoundHttpException(sprintf('Theme with name "%s" was not found in organization themes.', $themeName));
        }

        return reset($themes);
    }
}
