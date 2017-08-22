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
use SWP\Bundle\CoreBundle\Form\Type\ThemeUploadType;
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
     * @Route("/api/{version}/organization/themes/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_list_themes")
     *
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return ResourcesListResponse
     */
    public function listAction(Request $request)
    {
        $themeLoader = $this->get('swp_core.loader.organization.theme');
        $themes = $themeLoader->load();
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
     *         200="Returned on success."
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
            $themeUploader->upload($formData['file']);

            return new SingleResourceResponse($formData, new ResponseContext(201));
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
