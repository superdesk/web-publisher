<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Settings Bundle.
 *
 * Copyright 2017 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\SettingsBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Exception\InvalidScopeException;
use SWP\Bundle\SettingsBundle\Form\Type\BulkSettingsUpdateType;
use SWP\Bundle\SettingsBundle\Form\Type\SettingType;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends Controller
{
    /**
     * @Operation(
     *     tags={"settings"},
     *     summary="Lists all settings",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\Settings::class, groups={"api"}))
     *         )
     *     )
     * )
     *
     * @Route("/api/{version}/settings/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_settings_list")
     *
     * @return SingleResourceResponse
     */
    public function listAction()
    {
        $settingsManager = $this->get('swp_settings.manager.settings');

        return new SingleResourceResponse($settingsManager->all());
    }

    /**
     * Revert settings to defaults by scope.
     *
     * @Operation(
     *     tags={"settings"},
     *     summary="Revert settings to defaults by scope",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned on success."
     *     )
     * )
     *
     * @Route("/api/{version}/settings/revert/{scope}", methods={"POST"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_settings_revert")
     *
     * @return SingleResourceResponse
     */
    public function revertAction(string $scope)
    {
        $settingsManager = $this->get('swp_settings.manager.settings');

        $settingsManager->clearAllByScope($scope);

        return new SingleResourceResponse(null, new ResponseContext(204));
    }

    /**
     * @Operation(
     *     tags={"settings"},
     *     summary="Change setting value",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=SettingType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @Model(type=\SWP\Bundle\CoreBundle\Model\Settings::class, groups={"api"})
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Setting not found"
     *     )
     * )
     *
     * @Route("/api/{version}/settings/", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_settings_update")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function updateAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('', SettingType::class, [], [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingsManager = $this->get('swp_settings.manager.settings');
            $scopeContext = $this->get('swp_settings.context.scope');
            $data = $form->getData();

            $setting = $settingsManager->getOneSettingByName($data['name']);

            if (null === $setting) {
                throw new NotFoundHttpException('Setting with this name was not found.');
            }

            $scope = $setting['scope'];
            $owner = null;
            if (ScopeContextInterface::SCOPE_GLOBAL !== $scope) {
                $owner = $scopeContext->getScopeOwner($scope);
                if (null === $owner) {
                    throw new InvalidScopeException($scope);
                }
            }

            $setting = $settingsManager->set($data['name'], $data['value'], $scope, $owner);

            return new SingleResourceResponse($setting);
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }

    /**
     * Settings bulk update - update multiple settings.
     *
     * @Operation(
     *     tags={"settings"},
     *     summary="Settings bulk update",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             ref=@Model(type=BulkSettingsUpdateType::class)
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned on success.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=\SWP\Bundle\CoreBundle\Model\Settings::class, groups={"api"}))
     *         )
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Setting not found"
     *     )
     * )
     *
     * @Route("/api/{version}/settings/bulk/", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v2"}, name="swp_api_settings_bulk_update")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function bulkAction(Request $request)
    {
        $form = $this->get('form.factory')->createNamed('', BulkSettingsUpdateType::class, [], [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingsManager = $this->get('swp_settings.manager.settings');
            $scopeContext = $this->get('swp_settings.context.scope');
            $data = $form->getData();

            foreach ((array) $data['bulk'] as $item) {
                $setting = $settingsManager->getOneSettingByName($item['name']);
                if (null === $setting) {
                    throw new NotFoundHttpException(sprintf('Setting with "%s" name was not found.', $item['name']));
                }

                $scope = $setting['scope'];
                $owner = null;
                if (ScopeContextInterface::SCOPE_GLOBAL !== $scope) {
                    $owner = $scopeContext->getScopeOwner($scope);
                    if (null === $owner) {
                        throw new InvalidScopeException($scope);
                    }
                }

                $settingsManager->set($item['name'], $item['value'], $scope, $owner);
            }

            return new SingleResourceResponse($settingsManager->all());
        }

        return new SingleResourceResponse($form, new ResponseContext(400));
    }
}
