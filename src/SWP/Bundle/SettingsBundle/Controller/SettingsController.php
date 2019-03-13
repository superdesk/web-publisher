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

use SWP\Bundle\SettingsBundle\Context\ScopeContextInterface;
use SWP\Bundle\SettingsBundle\Exception\InvalidScopeException;
use SWP\Bundle\SettingsBundle\Form\Type\BulkSettingsUpdateType;
use SWP\Bundle\SettingsBundle\Form\Type\SettingType;
use SWP\Component\Common\Response\ResponseContext;
use SWP\Component\Common\Response\SingleResourceResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/api/{version}/settings/", methods={"GET"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_settings_list")
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
     * @ApiDoc(
     *     resource=true,
     *     description="Revert settings to defaults by scope",
     *     statusCodes={
     *         204="Returned on success."
     *     }
     * )
     * @Route("/api/{version}/settings/revert/{scope}", methods={"POST"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_settings_revert")
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
     * Change setting value.
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Change setting value",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Setting not found",
     *     },
     *     input="SWP\Bundle\SettingsBundle\Form\Type\SettingType"
     * )
     * @Route("/api/{version}/settings/", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_settings_update")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function updateAction(Request $request)
    {
        $form = $this->createForm(SettingType::class, [], [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
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
     * @ApiDoc(
     *     resource=true,
     *     description="Settings bulk update",
     *     statusCodes={
     *         200="Returned on success.",
     *         404="Setting not found",
     *     },
     *     input="SWP\Bundle\SettingsBundle\Form\Type\BulkSettingsUpdateType"
     * )
     * @Route("/api/{version}/settings/bulk/", methods={"PATCH"}, options={"expose"=true}, defaults={"version"="v1"}, name="swp_api_settings_bulk_update")
     *
     * @param Request $request
     *
     * @return SingleResourceResponse
     */
    public function bulkAction(Request $request)
    {
        $form = $this->createForm(BulkSettingsUpdateType::class, [], [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
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
