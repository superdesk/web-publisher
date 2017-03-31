<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\Controller;

use SWP\Bundle\CoreBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SWP\Component\Common\Response\ResourcesListResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends Controller
{
    /**
     * Lists all settings.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Lists all settings",
     *     statusCodes={
     *         200="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/settings/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_settings_list")
     * @Method("GET")
     * Cache(expires="10 minutes", public=true)
     *
     * @param Request $request
     *
     * @return ResourcesListResponse
     */
    public function listAction(Request $request)
    {
        $settingsManager = $this->get('swp_settings.manager.settings');

        return new JsonResponse($settingsManager->all());
    }

    /**
     * Change setting value.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Change setting value",
     *     statusCodes={
     *         201="Returned on success.",
     *         404="Setting not found",
     *     },
     *     input="SWP\Bundle\TemplatesSystemBundle\Form\Type\ContainerType"
     * )
     * @Route("/api/{version}/settings/", options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_settings_update")
     * @Method("PATCH")
     *
     * @param Request $request
     * @param string  $uuid
     *
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     *
     * @return SingleResourceResponse
     */
    public function updateAction(Request $request)
    {
        $settingsManager = $this->get('swp_settings.manager.settings');
        $container = $this->getContainerForUpdate($uuid);
        $user = null;
        if ($this->getUser() instanceof UserInterface) {
            $user = $this->getUser();
        }

        $tenant = $this->get('swp_multi_tenancy.tenant_context')->getTenant();
        $organization = $tenant->getOrganization();

        return new SingleResourceResponse([]);
    }
}
