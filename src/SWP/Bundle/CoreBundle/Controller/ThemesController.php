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

use Knp\Component\Pager\Pagination\SlidingPagination;
use Nelmio\ApiDocBundle\Annotation\Model;
use SWP\Bundle\CoreBundle\Form\Type\ThemeInstallType;
use SWP\Bundle\CoreBundle\Form\Type\ThemeUploadType;
use SWP\Bundle\CoreBundle\Model\Tenant;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Theme\Helper\ThemeHelper;
use SWP\Component\Common\Response\ResourcesListResponseInterface;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use SWP\Component\Common\Response\SingleResourceResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Component\HttpFoundation\Request;

class ThemesController extends Controller
{
    /**
     * @Operation(
     *     tags={"theme"},
     *     summary="Lists all available themes in organization",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Theme\Model\Theme::class, groups={"api"}))
     *         )
     *     )
     * )
     *
     * @Route("/api/{version}/organization/themes/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_list_available_themes")
     */
    public function listAvailableAction(): ResourcesListResponseInterface
    {
        $themeLoader = $this->get('swp_core.loader.organization.theme');
        $themes = $themeLoader->load();
        $pagination = new SlidingPagination();
        $pagination->setItems($themes);
        $pagination->setTotalItemCount(count($themes));

        return new ResourcesListResponse($pagination);
    }

    /**
     * @Operation(
     *     tags={"theme"},
     *     summary="Lists all available themes in tenant",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Theme\Model\Theme::class, groups={"api"}))
     *         )
     *     )
     * )
     *
     * @Route("/api/{version}/themes/", options={"expose"=true}, defaults={"version"="v2"}, methods={"GET"}, name="swp_api_list_tenant_themes")
     */
    public function listInstalledAction(): ResourcesListResponseInterface
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->get('swp_multi_tenancy.tenant_context')->getTenant();
        $tenantCode = $tenant->getCode();
        $themes = array_filter(
            $this->get('sylius.repository.theme')->findAll(),
            static function ($element) use (&$tenantCode) {
                if (strpos($element->getName(), ThemeHelper::SUFFIX_SEPARATOR.$tenantCode)) {
                    return true;
                }
            }
        );

        $pagination = new SlidingPagination();
        $pagination->setItems($themes);
        $pagination->setTotalItemCount(count($themes));

        return new ResourcesListResponse($pagination);
    }

    /**
     * Upload new theme to organization.
     *
     * @Operation(
     *     tags={"theme"},
     *     summary="Upload new theme to organization",
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="file"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success."
     *     )
     * )
     *
     * @Route("/api/{version}/organization/themes/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_upload_theme")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function uploadThemeAction(Request $request)
    {
        $form = $form = $this->get('form.factory')->createNamed('', ThemeUploadType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $themeUploader = $this->container->get('swp_core.uploader.theme');

            try {
                $themePath = $themeUploader->upload($formData['file']);
            } catch (\Exception $e) {
                return new SingleResourceResponse(['message' => $e->getMessage()], new ResponseContext(400));
            }
            $themeConfig = json_decode(file_get_contents($themePath.DIRECTORY_SEPARATOR.'theme.json'), true);

            return new SingleResourceResponse($themeConfig, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * @Operation(
     *     tags={"theme"},
     *     summary="Install theme for tenant",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=ThemeInstallType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned on success."
     *     )
     * )
     *
     * @Route("/api/{version}/themes/", options={"expose"=true}, defaults={"version"="v2"}, methods={"POST"}, name="swp_api_install_theme")
     */
    public function installThemeAction(Request $request): SingleResourceResponseInterface
    {
        $form = $form = $this->get('form.factory')->createNamed('', ThemeInstallType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $themeService = $this->container->get('swp_core.service.theme');
            [$sourceDir, $themeDir] = $themeService->getDirectoriesForTheme($formData['name']);
            $themeService->installAndProcessGeneratedData($sourceDir, $themeDir, $formData['processGeneratedData']);

            return new SingleResourceResponse(['status' => 'installed'], new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
