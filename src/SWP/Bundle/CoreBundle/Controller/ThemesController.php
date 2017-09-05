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
use SWP\Bundle\CoreBundle\Form\Type\ThemeInstallType;
use SWP\Bundle\CoreBundle\Form\Type\ThemeUploadType;
use SWP\Bundle\CoreBundle\Model\TenantInterface;
use SWP\Bundle\CoreBundle\Theme\Helper\ThemeHelper;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Component\HttpFoundation\Request;

class ThemesController extends Controller
{
    /**
     * Lists all available themes in organization.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all available themes in organization",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/organization/themes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_list_available_themes")
     *
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return ResourcesListResponse
     */
    public function listAvailableAction(Request $request)
    {
        $themeLoader = $this->get('swp_core.loader.organization.theme');
        $themes = $themeLoader->load();
        $pagination = new SlidingPagination();
        $pagination->setItems($themes);
        $pagination->setTotalItemCount(count($themes));

        return new ResourcesListResponse($pagination);
    }

    /**
     * Lists all installed themes in tenant.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all available themes in organization",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/themes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_list_tenant_themes")
     *
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return ResourcesListResponse
     */
    public function listInstalledAction(Request $request)
    {
        /** @var TenantInterface $tenant */
        $tenant = $this->get('swp_multi_tenancy.tenant_context')->getTenant();
        $tenantCode = $tenant->getCode();
        $currentTheme = $tenant->getThemeName();
        $themes = array_filter(
            $this->get('sylius.repository.theme')->findAll(),
            function ($element) use (&$tenantCode, $currentTheme) {
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
     * @ApiDoc(
     *     resource=true,
     *     description="Upload new theme to organization",
     *     statusCodes={
     *         201="Returned on success."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\ThemeUploadType"
     * )
     * @Route("/api/{version}/organization/themes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_upload_theme")
     *
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function uploadThemeAction(Request $request)
    {
        $form = $this->createForm(ThemeUploadType::class, []);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $formData = $form->getData();
            $themeUploader = $this->container->get('swp_core.uploader.theme');
            $themePath = $themeUploader->upload($formData['file']);
            $themeConfig = json_decode(file_get_contents($themePath.DIRECTORY_SEPARATOR.'theme.json'), true);

            return new SingleResourceResponse($themeConfig, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Install theme for tenant.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Install theme for tenant",
     *     statusCodes={
     *         201="Returned on success."
     *     },
     *     input="SWP\Bundle\CoreBundle\Form\Type\ThemeInstallType"
     * )
     * @Route("/api/{version}/themes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_install_theme")
     *
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function installThemeAction(Request $request)
    {
        $form = $this->createForm(ThemeInstallType::class, []);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $formData = $form->getData();
            $themeInstaller = $this->container->get('swp_core.installer.theme');
            $theme = $themeInstaller->install($formData['name']);

            return new SingleResourceResponse($theme, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
